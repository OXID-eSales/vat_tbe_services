<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Controller;

use OxidEsales\Eshop\Application\Model\Basket as EShopBasket;
use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\BasketVATValidator;
use OxidEsales\EVatModule\Model\User;
use OxidEsales\EVatModule\Service\BasketItemsValidator;
use OxidEsales\EVatModule\Shop\Basket;
use OxidEsales\EVatModule\Shop\Order;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * Hooks to order class to get events.
 */
class OrderController extends OrderController_parent
{
    use ServiceContainer;

    /**
     * Executes parent::render(), creates list with basket articles
     * Returns name of template file basket::_sThisTemplate (for Search
     * engines return "content.tpl" template to avoid fake orders etc).
     *
     * @return  string   $this->_sThisTemplate  current template file name
     */
    public function render()
    {
        $oUserCountry = $this->getServiceFromContainer(User::class);
        $oBasket = $this->getBasket();
        if ($this->getUser() && !$oUserCountry->isUserFromDomesticCountry() && $oBasket) {
            /** @var BasketItemsValidator $oVATTBEBasketItemsValidator */
            $oVATTBEBasketItemsValidator = $this->getServiceFromContainer(BasketItemsValidator::class);
            $oVATTBEBasketItemsValidator->validateTbeArticlesAndShowMessageIfNeeded('order');
        }

        return parent::render();
    }

    /**
     * Returns next order step. If ordering was successful - returns string "thankyou" (possible
     * additional parameters), otherwise - returns string "payment" with additional
     * error parameters.
     *
     * @param integer $iSuccess status code
     *
     * @return  string  $sNextStep  partial parameter url for next step
     */
    protected function getNextStep($iSuccess)
    {
        if ($iSuccess == Order::ORDER_STATE_TBE_NOT_CONFIGURED) {
            $sNextStep = 'order';
        } else {
            $sNextStep = parent::getNextStep($iSuccess);
        }

        return $sNextStep;
    }

    /**
     * Format message if basket has tbe articles
     *
     * @return string
     */
    public function getOeVATTBEMarkMessage()
    {
        /** @var EShopBasket|Basket $oBasket */
        $oBasket = Registry::getSession()->getBasket();
        $oMarkGenerator = $this->getBasketContentMarkGenerator();
        $oCountry = $oBasket->getOeVATTBECountry();
        $sCountryName = $oCountry ? $oCountry->getOeVATTBEName() : '';

        $sMessage = $oMarkGenerator->getMark('tbeService') . ' - ';
        $sMessage .= sprintf(Registry::getLang()->translateString('OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY'), $sCountryName);

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
}
