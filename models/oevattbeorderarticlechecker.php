<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
     * @return array|oxArticleList
     */
    protected function _getArticleList()
    {
        return $this->_mArticleList;
    }
}
