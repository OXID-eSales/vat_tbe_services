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
use OxidEsales\Eshop\Core\Registry;

/**
 * VAT TBE order articles checker class
 */
class OrderArticleChecker
{
    /** @var array List of incorrect TBE articles */
    private $_aInvalidArticles = null;

    /**
     * Handles dependencies.
     *
     * @param User  $oTBEUserCountry TBE user country
     * @param array $mArticleList    Articles list to check.
     */
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
