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
    private $_getBasketItemVATFormatter = null;

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
        $oVATTBEBasketItemsValidator->validateTbeArticlesAndShowMessageIfNeeded('basket');

        return parent::render();
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
        $oMarkGenerator =  $this->getBasketContentMarkGenerator();

        if ($oBasket->hasVATTBEArticles()) {
            if (!$oBasket->getUser()) {
                $sMessage = $oMarkGenerator->getMark('tbeService') . ' - ';
                $sMessage .= oxRegistry::getLang()->translateString('OEVATTBE_VAT_WILL_BE_CALCULATED_BY_USER_COUNTRY');
            } elseif ($oBasket->isTBEValid()) {
                if ($oCountry && $oCountry->appliesTBEVAT()) {
                    $sMessage = $oMarkGenerator->getMark('tbeService') . ' - ';
                    $sMessage .= sprintf(oxRegistry::getLang()->translateString('OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY'), $oCountry->getVATTBEName());
                }
            }
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
    public function getTBEVatFormatted($oBasketItem)
    {
        $sMessage = $this->_getBasketItemVATFormatter()->formatVAT($oBasketItem);

        return $sMessage;
    }

    /**
     * Returns vat formatter
     *
     * @return oeVATTBEBasketItemVATFormatter
     */
    protected function _getBasketItemVATFormatter()
    {
        if (is_null($this->_getBasketItemVATFormatter)) {
            $oBasket = $this->getSession()->getBasket();
            $oMarkGenerator =  $this->getBasketContentMarkGenerator();
            $this->_getBasketItemVATFormatter = oxNew('oeVATTBEBasketItemVATFormatter', $oBasket, $oMarkGenerator);
        }

        return $this->_getBasketItemVATFormatter;
    }
}
