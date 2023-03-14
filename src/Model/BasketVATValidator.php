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
use OxidEsales\Eshop\Core\Session;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\EVatModule\Model\User as UserModel;

/**
 * Class for validating VAT for basket items.
 */
class BasketVATValidator
{
    private EShopBasket $basket;

    private ?EShopUser $user;

    /**
     * Handles class dependencies.
     *
     * @param UserModel $userCountry TBE User country object.
     */
    public function __construct(
        Session $session,
        private UserModel $userCountry,
    ) {
        $this->basket = $session->getBasket();
        $this->user = $session->getUser() ?: null;
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

        /** @var EShopArticle|Article $oArticle */
        $oArticle = $oBasketItem->getArticle();
        $aInValidArticles = $this->basket->getOeVATTBEInValidArticles();
        $blIsArticleInvalid = isset($aInValidArticles[$oArticle->getId()]);

        if ($this->user && !$this->userCountry->isUserFromDomesticCountry() && !$this->basket->isOeVATTBEValid() && $blIsArticleInvalid) {
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

        $oCountry = $this->basket->getOeVATTBECountry();

        /** @var EShopArticle|Article $oArticle */
        $oArticle = $oBasketItem->getArticle();
        if ($oArticle->isOeVATTBETBEService() && !$this->userCountry->isUserFromDomesticCountry() && (!$this->user || ($oCountry && $oCountry->appliesOeTBEVATTbeVat()))) {
            $blShowMark = true;
        }

        return $blShowMark;
    }
}
