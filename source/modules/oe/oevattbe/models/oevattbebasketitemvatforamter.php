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
 * VAT TBE oxBasket class
 */
class oeVATTBEBasketItemVATFormatter extends oeVatTbeOxBasket_parent
{
    private $_oBasket;
    private $_oBasketItem;
    private $_oMarkGenerator;

    /**
     * Constructor
     *
     * @param oxBasketItem                         $oBasketItem    Basket item
     * @param oeVATTBEOxBasket                     $oBasket        Basket
     * @param oeVATTBEOxBasketContentMarkGenerator $oMarkGenerator Mark generator
     */
    public function __construct($oBasketItem, $oBasket, $oMarkGenerator)
    {
        $this->_oBasketItem = $oBasketItem;
        $this->_oBasket = $oBasket;
        $this->_oMarkGenerator = $oMarkGenerator;
    }

    /**
     * Return formatted vat rate
     *
     * @return string
     */
    public function getTBEVat()
    {
        $oBasketItem = $this->_oBasketItem;
        $oBasket = $this->_oBasket;
        $oMarkGenerator =  $this->_oMarkGenerator;

        $sMessage = $oBasketItem->getVatPercent() . '%';
        $oArticle = $oBasketItem->getArticle();
        $oCountry = $oBasket->getTBECountry();
        $aInValidArticles = $oBasket->getTBEInValidArticles();

        if ($oArticle->isTBEService()) {
            if ($oBasket->getUser()) {
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
