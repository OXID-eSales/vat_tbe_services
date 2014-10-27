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
        /** @var oeVATTBEBasketItemsValidator $oVATTBEBasketItemsValidator */
        $oVATTBEBasketItemsValidator = oeVATTBEBasketItemsValidator::getInstance($this->getBasketArticles());
        $oVATTBEBasketItemsValidator->validateTbeArticlesAndShowMessageIfNeeded('order');

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
    public function getTBEMarkMessage()
    {
        $sMessage ='';
        $oBasket = $this->getSession()->getBasket();
        $oCountry = $oBasket->getTBECountry();
        $oMarkGenerator = $this->getBasketContentMarkGenerator();

        if ($oBasket->hasVATTBEArticles() && $oBasket->isTBEValid() && $oCountry && $oCountry->appliesTBEVAT()) {
            $sMessage = $oMarkGenerator->getMark('tbeService') . ' - ';
            $sMessage .= sprintf(oxRegistry::getLang()->translateString('OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY'), $oCountry->getVATTBEName());
        }

        return $sMessage;
    }

    /**
     * Return formatted vat rate
     *
     * @param oxBasketItem $oBasketItem - basket item
     *
     * @return string
     */
    public function getTBEVat($oBasketItem)
    {
        $sMessage = $oBasketItem->getVatPercent() . '%';

        $oArticle = $oBasketItem->getArticle();
        $oBasket = $this->getSession()->getBasket();
        $oCountry = $oBasket->getTBECountry();
        $oMarkGenerator =  $this->getBasketContentMarkGenerator();
        $aInValidArticles = $oBasket->getTBEInValidArticles();

        if ($oArticle->isTBEService()) {
            if ($this->getUser()) {
                $sMessage .= ($oCountry->appliesTBEVAT()) ? $oMarkGenerator->getMark('tbeService') : '';
                if (!$oBasket->isTBEValid() && isset($aInValidArticles[$oArticle->getId()])) {
                    $sMessage = '-';
                }
            } else {
                $sMessage .= $oMarkGenerator->getMark('tbeService');
            }
        }

        return $sMessage;
    }
}
