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
 * Class for validating VAT for basket items.
 */
class oeVATTBEBasketVATValidator
{
    /** @var oeVATTBEOxBasket|oxBasket  */
    private $_oBasket;

    /** @var oxUser|oeVATTBEOxUser */
    private $_oUser;

    /** @var oeVATTBETBEUser */
    private $_oTBEUserCountry;

    /**
     * Handles class dependencies.
     *
     * @param oeVATTBEOxBasket $oBasket         Basket object.
     * @param oeVATTBEOxUser   $oUser           User object.
     * @param oeVATTBETBEUser  $oTBEUserCountry TBE User country object.
     */
    public function __construct($oBasket, $oUser, $oTBEUserCountry)
    {
        $this->_oBasket = $oBasket;
        $this->_oUser = $oUser;
        $this->_oTBEUserCountry = $oTBEUserCountry;
    }

    /**
     * Creates class instance with default dependencies.
     *
     * @return oeVATTBEBasketVATValidator
     */
    public static function createInstance()
    {
        $oBasket = oxRegistry::getSession()->getBasket();
        $oUser = oxRegistry::getSession()->getUser();
        $oTBEUserCountry = oeVATTBETBEUser::createInstance();

        /** @var oeVATTBEBasketVATValidator $oValidator */
        $oValidator = oxNew('oeVATTBEBasketVATValidator', $oBasket, $oUser, $oTBEUserCountry);

        return $oValidator;
    }

    /**
     * Return formatted vat rate
     *
     * @param oxBasketItem $oBasketItem - basket item
     *
     * @return string
     */
    public function isArticleValid($oBasketItem)
    {
        $blValid = true;

        $oUserCountry = $this->_getTBEUserCountry();
        $oBasket = $this->_getBasket();
        $oUser = $this->_getUser();

        /** @var oxArticle|oeVATTBEOxArticle $oArticle */
        $oArticle = $oBasketItem->getArticle();
        $aInValidArticles = $oBasket->getOeVATTBEInValidArticles();
        $blIsArticleInvalid = isset($aInValidArticles[$oArticle->getId()]);

        if ($oUser && !$oUserCountry->isUserFromDomesticCountry() && !$oBasket->isOeVATTBEValid() && $blIsArticleInvalid) {
            $blValid = false;
        }

        return $blValid;
    }

    /**
     * Return formatted vat rate
     *
     * @param oxBasketItem $oBasketItem - basket item
     *
     * @return string
     */
    public function showVATTBEMark($oBasketItem)
    {
        $blShowMark = false;

        $oUserCountry = $this->_getTBEUserCountry();
        $oBasket = $this->_getBasket();
        $oUser = $this->_getUser();
        $oCountry = $oBasket->getOeVATTBECountry();

        /** @var oxArticle|oeVATTBEOxArticle $oArticle */
        $oArticle = $oBasketItem->getArticle();
        if ($oArticle->isOeVATTBETBEService() && !$oUserCountry->isUserFromDomesticCountry() && (!$oUser || ($oCountry && $oCountry->appliesOeTBEVATTbeVat()))) {
            $blShowMark = true;
        }

        return $blShowMark;
    }

    /**
     * Returns oxBasket object
     *
     * @return oeVATTBEOxBasket|oxBasket
     */
    protected function _getBasket()
    {
        return $this->_oBasket;
    }

    /**
     * Returns oxUser object.
     *
     * @return oxUser
     */
    protected function _getUser()
    {
        return $this->_oUser;
    }

    /**
     * Returns oeVATTBETBEUser object.
     *
     * @return oeVATTBETBEUser
     */
    protected function _getTBEUserCountry()
    {
        return $this->_oTBEUserCountry;
    }
}
