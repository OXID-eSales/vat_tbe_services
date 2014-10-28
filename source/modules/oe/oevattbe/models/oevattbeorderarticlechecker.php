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
 * VAT TBE order articles checker class
 */
class oeVATTBEOrderArticleChecker
{
    /** @var array|oxArticleList */
    private $_mArticleList = null;

    /** @var oeVATTBETBEUser */
    private $_oTBEUserCountry = null;

    /** @var array List of incorrect TBE articles */
    private $_aInvalidArticles = null;

    /**
     * Handles dependencies.
     *
     * @param array|oxArticleList $mArticleList    Articles list to check.
     * @param oeVATTBETBEUser     $oTBEUserCountry TBE user country
     */
    public function __construct($mArticleList, $oTBEUserCountry)
    {
        if (!is_array($mArticleList) && !($mArticleList instanceof oxArticleList)) {
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

        if ($isValid && (!$oCountry->isInEU() || !$oCountry->appliesTBEVAT())) {
            return true;
        }

        if ($isValid && $oCountry->appliesTBEVAT()) {
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
                if ($oArticle->isTBEService() && is_null($oArticle->oeVATTBEgetTBEVat())) {
                    $this->_aInvalidArticles[$oArticle->getId()] = $oArticle;
                }
            }
        }

        return $this->_aInvalidArticles;
    }

    /**
     * Returns articles to work with.
     *
     * @return array|oxArticleList
     */
    protected function _getArticleList()
    {
        return $this->_mArticleList;
    }
}
