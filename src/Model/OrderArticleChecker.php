<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model;

use OxidEsales\Eshop\Application\Model\Article as EShopArticle;
use OxidEsales\Eshop\Core\Registry;

/**
 * VAT TBE order articles checker class
 */
class OrderArticleChecker
{
    /** @var array List of incorrect TBE articles */
    private $_aInvalidArticles = null;

    public function __construct(
        private User $user
    ) {
    }

    /**
     * Checks if all articles are valid.
     * Article is considered invalid if it is a TBE article and
     * article's VAT can not be calculated for user's country.
     *
     * @return bool
     */
    public function isValid()
    {
        $oCountry = $this->user->getCountry();

        $isValid = $oCountry ? true : false;

        if ($isValid && (!$oCountry->isInEU() || !$oCountry->appliesOeTBEVATTbeVat())) {
            return true;
        }

        if ($isValid && $oCountry->appliesOeTBEVATTbeVat()) {
            $aInvalidArticles = $this->getInvalidArticles();
            $isValid = empty($aInvalidArticles);
        }

        return $isValid;
    }

    /**
     * Returns list of invalid articles.
     * Article is considered invalid if it is a TBE article and
     * article's VAT can not be calculated for user's country.
     *
     * @return array
     */
    public function getInvalidArticles()
    {
        if (is_null($this->_aInvalidArticles)) {
            $basket = Registry::getSession()->getBasket();
            $basketArticles = $basket->getBasketArticles();

            $this->_aInvalidArticles = array();
            foreach ($basketArticles as $basketArticle) {
                /** @var EShopArticle $basketArticle */
                if ($basketArticle->isOeVATTBETBEService() && is_null($basketArticle->getOeVATTBETBEVat())) {
                    $this->_aInvalidArticles[$basketArticle->getId()] = $basketArticle;
                }
            }
        }

        return $this->_aInvalidArticles;
    }
}
