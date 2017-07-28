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
 * Hooks to order class to get events.
 */
class oeVATTBEOrder extends oeVATTBEOrder_parent
{
    /**
     * Executes parent::render(), creates list with basket articles
     * Returns name of template file basket::_sThisTemplate (for Search
     * engines return "content.tpl" template to avoid fake orders etc).
     *
     * @return  string   $this->_sThisTemplate  current template file name
     */
    public function render()
    {
        $oUserCountry = oeVATTBETBEUser::createInstance();
        $oBasket = $this->getBasket();
        if ($this->getUser() && !$oUserCountry->isUserFromDomesticCountry() && $oBasket) {
            $oBasketArticles = $oBasket->getBasketArticles();
            /** @var oeVATTBEBasketItemsValidator $oVATTBEBasketItemsValidator */
            $oVATTBEBasketItemsValidator = oeVATTBEBasketItemsValidator::createInstance($oBasketArticles);
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
    protected function _getNextStep($iSuccess)
    {
        if ($iSuccess == oeVATTBEOxOrder::ORDER_STATE_TBE_NOT_CONFIGURED) {
            $sNextStep = 'order';
        } else {
            $sNextStep = parent::_getNextStep($iSuccess);
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
        /** @var oxBasket|oeVATTBEOxBasket $oBasket */
        $oBasket = $this->getSession()->getBasket();
        $oMarkGenerator = $this->getBasketContentMarkGenerator();
        $oCountry = $oBasket->getOeVATTBECountry();
        $sCountryName = $oCountry ? $oCountry->getOeVATTBEName() : '';

        $sMessage = $oMarkGenerator->getMark('tbeService') . ' - ';
        $sMessage .= sprintf(oxRegistry::getLang()->translateString('OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY'), $sCountryName);

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
}
