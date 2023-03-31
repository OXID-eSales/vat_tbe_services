<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
