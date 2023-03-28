<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Hooks to basket class to get events.
 */
class oeVATTBEBasket extends oeVATTBEBasket_parent
{
    /**
     * Executes parent::render(), creates list with basket articles
     * Returns name of template file basket::_sThisTemplate (for Search
     * engines return "content.tpl" template to avoid fake orders etc).
     *
     * @return  string   $this->_sThisTemplate  current template file name.
     */
    public function render()
    {
        $oUserCountry = oeVATTBETBEUser::createInstance();
        $oBasket = $this->getSession()->getBasket();
        if ($this->getUser() && !$oUserCountry->isUserFromDomesticCountry() && $oBasket) {
            $oBasketArticles = $oBasket->getBasketArticles();
            /** @var oeVATTBEBasketItemsValidator $oVATTBEBasketItemsValidator */
            $oVATTBEBasketItemsValidator = oeVATTBEBasketItemsValidator::createInstance($oBasketArticles);
            $oVATTBEBasketItemsValidator->validateTbeArticlesAndShowMessageIfNeeded('basket');
        }

        return parent::render();
    }

    /**
     * Returns TBE Articles VAT explanation message.
     *
     * @return string
     */
    public function getOeVATTBEMarkMessage()
    {
        $oMarkGenerator =  $this->getBasketContentMarkGenerator();

        $sMessage = $oMarkGenerator->getMark('tbeService') . ' - ';
        if (!$this->getUser()) {
            $sMessage .= $this->_getOeVATTBEMarkMessageForAnonymousUser();
        } else {
            $sMessage .= $this->_getOeVATTBEMarkExplanationForLoggedInUser();
        }

        return $sMessage;
    }

    /**
     * Return whether to show VAT TBE Mark message.
     *
     * @return bool
     */
    public function oeVATTBEShowVATTBEMarkMessage()
    {
        /** @var oxBasket|oeVATTBEOxBasket $oBasket */
        $oBasket = $this->getSession()->getBasket();
        $oCountry = $oBasket->getOeVATTBECountry();
        $oTBEUserCountry = oeVATTBETBEUser::createInstance();

        $blBasketValid = $oBasket->hasOeTBEVATArticles();
        $blCountryAppliesTBEVAT = !$oCountry || $oCountry->appliesOeTBEVATTbeVat();

        return !$oTBEUserCountry->isUserFromDomesticCountry() && $blBasketValid && $blCountryAppliesTBEVAT;
    }

    /**
     * Return formatted vat rate
     *
     * @param oxBasketItem $oBasketItem - basket item
     *
     * @return bool
     */
    public function isOeVATTBETBEArticleValid($oBasketItem)
    {
        $oValidator = oeVATTBEBasketVATValidator::createInstance();

        return $oValidator->isArticleValid($oBasketItem);
    }

    /**
     * Return formatted vat rate
     *
     * @param oxBasketItem $oBasketItem - basket item
     *
     * @return bool
     */
    public function oeVATTBEShowVATTBEMark($oBasketItem)
    {
        $oValidator = oeVATTBEBasketVATValidator::createInstance();

        return $oValidator->showVATTBEMark($oBasketItem);
    }

    /**
     * Forms VAT TBE Articles Mark message for anonymous user.
     *
     * @return string
     */
    private function _getOeVATTBEMarkMessageForAnonymousUser()
    {
        /** @var oeVATTBEoxShop $oShop */
        $oShop = oxNew('oxShop');
        $oLang = oxRegistry::getLang();

        $oDomesticCountry = $oShop->getOeVATTBEDomesticCountry();
        $sCountryName = $oDomesticCountry ? $oDomesticCountry->getOeVATTBEName(): '';

        return sprintf($oLang->translateString('OEVATTBE_VAT_WILL_BE_CALCULATED_BY_USER_COUNTRY'), $sCountryName);
    }

    /**
     * Forms VAT TBE Articles Mark message for logged in user.
     *
     * @return string
     */
    private function _getOeVATTBEMarkExplanationForLoggedInUser()
    {
        /** @var oxBasket|oeVATTBEOxBasket $oBasket */
        $oBasket = $this->getSession()->getBasket();
        $oLang = oxRegistry::getLang();

        $oCountry = $oBasket->getOeVATTBECountry();
        $sCountryName = $oCountry ? $oCountry->getOeVATTBEName() : '';
        return sprintf($oLang->translateString('OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY'), $sCountryName);
    }
}
