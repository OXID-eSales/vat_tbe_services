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
class oeVATTBEBasketItemVATFormatter
{
    private $_oBasket;

    private $_oMarkGenerator;

    /**
     * Constructor
     *
     * @param oeVATTBEOxBasket                     $oBasket        Basket
     * @param oeVATTBEOxBasketContentMarkGenerator $oMarkGenerator Mark generator
     */
    public function __construct($oBasket, $oMarkGenerator)
    {

        $this->_oBasket = $oBasket;
        $this->_oMarkGenerator = $oMarkGenerator;
    }

    /**
     * Return formatted vat rate
     *
     * @param oxBasketItem $oBasketItem Basket item
     *
     * @return string
     */
    public function formatVAT($oBasketItem)
    {
        $oBasket = $this->_oBasket;
        $oMarkGenerator =  $this->_oMarkGenerator;

        $sMessage = $oBasketItem->getVatPercent() . '%';
        $oArticle = $oBasketItem->getArticle();
        $oCountry = $oBasket->getTBECountry();
        $aInValidArticles = $oBasket->getTBEInValidArticles();

        if ($oArticle->oeVATTBEisTBEService()) {
            if ($oBasket->getUser()) {
                $sMessage .= ($oCountry->appliesTBEVAT()) ? ' '.$oMarkGenerator->getMark('tbeService') : '';
                if (!$oBasket->isTBEValid() && isset($aInValidArticles[$oArticle->getId()])) {
                    $sMessage = '-';
                }
            } else {
                $sMessage .= ' '.$oMarkGenerator->getMark('tbeService');
            }
        }

        return $sMessage;
    }
}
