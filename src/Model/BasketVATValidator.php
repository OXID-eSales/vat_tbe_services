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

namespace OxidEsales\EVatModule\Model;

use OxidEsales\Eshop\Application\Model\Article as EShopArticle;
use OxidEsales\Eshop\Application\Model\Basket as EShopBasket;
use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Application\Model\User as EShopUser;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\EVatModule\Shop\Basket;
use OxidEsales\EVatModule\Shop\Country;
use OxidEsales\EVatModule\Shop\User;
use OxidEsales\EVatModule\Model\User as UserModel;

/**
 * Class for validating VAT for basket items.
 */
class BasketVATValidator
{
    /** @var EShopBasket|Basket  */
    private $_oBasket;

    /** @var EShopUser|User */
    private $_oUser;

    /** @var Country */
    private $_oTBEUserCountry;

    /**
     * Handles class dependencies.
     *
     * @param Basket    $oBasket         Basket object.
     * @param EShopUser $oUser           User object.
     * @param User   $oTBEUserCountry TBE User country object.
     */
    public function __construct(EShopBasket $oBasket, EShopUser $oUser, \OxidEsales\EVatModule\Model\User $oTBEUserCountry)
    {
        $this->_oBasket = $oBasket;
        $this->_oUser = $oUser;
        $this->_oTBEUserCountry = $oTBEUserCountry;
    }

    /**
     * Creates class instance with default dependencies.
     *
     * @return BasketVATValidator
     */
    public static function createInstance()
    {
        $oBasket = Registry::getSession()->getBasket();
        $oUser = Registry::getSession()->getUser();
        $oTBEUserCountry = UserModel::createInstance();

        /** @var BasketVATValidator $oValidator */
        $oValidator = oxNew(BasketVATValidator::class, $oBasket, $oUser, $oTBEUserCountry);

        return $oValidator;
    }

    /**
     * Return formatted vat rate
     *
     * @param BasketItem $oBasketItem - basket item
     *
     * @return string
     */
    public function isArticleValid($oBasketItem)
    {
        $blValid = true;

        $oUserCountry = $this->getTBEUserCountry();
        $oBasket = $this->getBasket();
        $oUser = $this->getUser();

        /** @var EShopArticle|Article $oArticle */
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
     * @param BasketItem $oBasketItem - basket item
     *
     * @return string
     */
    public function showVATTBEMark($oBasketItem)
    {
        $blShowMark = false;

        $oUserCountry = $this->getTBEUserCountry();
        $oBasket = $this->getBasket();
        $oUser = $this->getUser();
        $oCountry = $oBasket->getOeVATTBECountry();

        /** @var EShopArticle|Article $oArticle */
        $oArticle = $oBasketItem->getArticle();
        if ($oArticle->isOeVATTBETBEService() && !$oUserCountry->isUserFromDomesticCountry() && (!$oUser || ($oCountry && $oCountry->appliesOeTBEVATTbeVat()))) {
            $blShowMark = true;
        }

        return $blShowMark;
    }

    /**
     * Returns oxBasket object
     *
     * @return EShopBasket|Basket
     */
    protected function getBasket()
    {
        return $this->_oBasket;
    }

    /**
     * Returns oxUser object.
     *
     * @return User
     */
    protected function getUser()
    {
        return $this->_oUser;
    }

    /**
     * Returns oeVATTBETBEUser object.
     *
     * @return Country
     */
    protected function getTBEUserCountry()
    {
        return $this->_oTBEUserCountry;
    }
}
