<?php
/**
 * This file is part of OXID eSales eVAT module.
 *
 * OXID eSales eVAT module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales eVAT module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales eVAT module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
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
        if (!$oUserCountry->isUserFromDomesticCountry() && $oBasket = $this->getSession()->getBasket()) {
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

        $blBasketValid = $oBasket->hasOeTBEVATArticles() && $oBasket->isOeVATTBEValid();
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
