<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Controller;

use OxidEsales\Eshop\Application\Model\Basket as EShopBasket;
use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Application\Model\Shop as EShopShop;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\BasketVATValidator;
use OxidEsales\EVatModule\Model\User;
use OxidEsales\EVatModule\Service\BasketItemsValidator;
use OxidEsales\EVatModule\Shop\Basket;
use OxidEsales\EVatModule\Shop\Shop;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * Hooks to basket class to get events.
 */
class BasketController extends BasketController_parent
{
    use ServiceContainer;

    /**
     * Executes parent::render(), creates list with basket articles
     * Returns name of template file basket::_sThisTemplate (for Search
     * engines return "content.tpl" template to avoid fake orders etc).
     *
     * @return  string   $this->_sThisTemplate  current template file name.
     */
    public function render()
    {
        $oUserCountry = $this->getServiceFromContainer(User::class);
        $oBasket = Registry::getSession()->getBasket();
        if ($this->getUser() && !$oUserCountry->isUserFromDomesticCountry() && $oBasket) {
            /** @var BasketItemsValidator $oVATTBEBasketItemsValidator */
            $this
                ->getServiceFromContainer(BasketItemsValidator::class)
                ->validateTbeArticlesAndShowMessageIfNeeded('basket');
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
            $sMessage .= $this->getOeVATTBEMarkMessageForAnonymousUser();
        } else {
            $sMessage .= $this->getOeVATTBEMarkExplanationForLoggedInUser();
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
        /** @var EShopBasket|Basket $oBasket */
        $oBasket = Registry::getSession()->getBasket();
        $oCountry = $oBasket->getOeVATTBECountry();
        $oTBEUserCountry = $this->getServiceFromContainer(User::class);

        $blBasketValid = $oBasket->hasOeTBEVATArticles();
        $blCountryAppliesTBEVAT = !$oCountry || $oCountry->appliesOeTBEVATTbeVat();

        return !$oTBEUserCountry->isUserFromDomesticCountry() && $blBasketValid && $blCountryAppliesTBEVAT;
    }

    /**
     * Return formatted vat rate
     *
     * @param BasketItem $oBasketItem - basket item
     *
     * @return bool
     */
    public function isOeVATTBETBEArticleValid($oBasketItem)
    {
        return $this
            ->getServiceFromContainer(BasketVATValidator::class)
            ->isArticleValid($oBasketItem);
    }

    /**
     * Return formatted vat rate
     *
     * @param BasketItem $oBasketItem - basket item
     *
     * @return bool
     */
    public function oeVATTBEShowVATTBEMark($oBasketItem)
    {
        return $this
            ->getServiceFromContainer(BasketVATValidator::class)
            ->showVATTBEMark($oBasketItem);
    }

    /**
     * Forms VAT TBE Articles Mark message for anonymous user.
     *
     * @return string
     */
    private function getOeVATTBEMarkMessageForAnonymousUser()
    {
        /** @var Shop $oShop */
        $oShop = oxNew(EShopShop::class);
        $oLang = Registry::getLang();

        $oDomesticCountry = $oShop->getOeVATTBEDomesticCountry();
        $sCountryName = $oDomesticCountry ? $oDomesticCountry->getOeVATTBEName(): '';

        return sprintf($oLang->translateString('OEVATTBE_VAT_WILL_BE_CALCULATED_BY_USER_COUNTRY'), $sCountryName);
    }

    /**
     * Forms VAT TBE Articles Mark message for logged in user.
     *
     * @return string
     */
    private function getOeVATTBEMarkExplanationForLoggedInUser()
    {
        /** @var EShopBasket|Basket $oBasket */
        $oBasket = Registry::getSession()->getBasket();
        $oLang = Registry::getLang();

        $oCountry = $oBasket->getOeVATTBECountry();
        $sCountryName = $oCountry ? $oCountry->getOeVATTBEName() : '';
        return sprintf($oLang->translateString('OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY'), $sCountryName);
    }
}
