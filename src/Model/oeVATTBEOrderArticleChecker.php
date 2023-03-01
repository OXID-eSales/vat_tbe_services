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

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\ArticleList;
use OxidEsales\EVatModule\Shop\oeVATTBEOxArticle;

/**
 * VAT TBE order articles checker class
 */
class oeVATTBEOrderArticleChecker
{
    /** @var array|ArticleList */
    private $_mArticleList = null;

    /** @var oeVATTBETBEUser */
    private $_oTBEUserCountry = null;

    /** @var array List of incorrect TBE articles */
    private $_aInvalidArticles = null;

    /**
     * Handles dependencies.
     *
     * @param array|ArticleList $mArticleList    Articles list to check.
     * @param oeVATTBETBEUser     $oTBEUserCountry TBE user country
     */
    public function __construct(Article $mArticleList, oeVATTBETBEUser $oTBEUserCountry)
    {
        if (!is_array($mArticleList) && !($mArticleList instanceof ArticleList)) {
            $mArticleList = array();
        }
        $this->_mArticleList = $mArticleList;
        $this->_oTBEUserCountry = $oTBEUserCountry;
    }

    /**
     * Return tbe user
     *
     * @return oeVATTBETBEUser
     */
    public function getTBEUserCountry()
    {
        return $this->_oTBEUserCountry;
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
        $oTBEUserCountry = $this->getTBEUserCountry();
        $oCountry = $oTBEUserCountry->getCountry();

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
            $mArticleList = $this->_getArticleList();

            $this->_aInvalidArticles = array();
            foreach ($mArticleList as $oArticle) {
                /** @var oeVATTBEOxArticle $oArticle */
                if ($oArticle->isOeVATTBETBEService() && is_null($oArticle->getOeVATTBETBEVat())) {
                    $this->_aInvalidArticles[$oArticle->getId()] = $oArticle;
                }
            }
        }

        return $this->_aInvalidArticles;
    }

    /**
     * Returns articles to work with.
     *
     * @return array|ArticleList
     */
    protected function _getArticleList()
    {
        return $this->_mArticleList;
    }
}
