<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * VAT Groups handling class
 */
class oeVATTBEVATGroupArticleCacheInvalidator extends oeVATTBEModel
{
    /** @var oxCacheBackend Cache backend. */
    private $_oCacheBackend = null;

    /** @var oeVATTBEArticleVATGroupsList Used to get articles assigned to specific group. */
    private $_oArticleVATGroupsList = null;

    /**
     * Handles class dependencies.
     *
     * @param oeVATTBEArticleVATGroupsList $oArticleVATGroupsList Used to get articles assigned to specific group.
     * @param oxCacheBackend               $oCacheBackend         Cache backend
     */
    public function __construct($oArticleVATGroupsList, $oCacheBackend)
    {
        $this->_oArticleVATGroupsList = $oArticleVATGroupsList;
        $this->_oCacheBackend = $oCacheBackend;
    }

    /**
     * Creates instance of oeVATTBEVATGroupArticleCacheInvalidator.
     *
     * @return oeVATTBEVATGroupArticleCacheInvalidator
     */
    public static function createInstance()
    {
        $oCacheBackend = oxRegistry::get('oxCacheBackend');
        $ArticleGroupsList = oeVATTBEArticleVATGroupsList::createInstance();

        return oxNew('oeVATTBEVATGroupArticleCacheInvalidator', $ArticleGroupsList, $oCacheBackend);
    }

    /**
     * Clears cache for VAT group articles.
     *
     * @param string $sGroupId
     */
    public function invalidate($sGroupId)
    {
        $oArticleVATGroupsList = $this->_getArticleVATGroupsList();
        $aArticleIds = $oArticleVATGroupsList->getArticlesAssignedToGroup($sGroupId);

        /** @var oxArticle $oArticle */
        $oArticle = oxNew('oxArticle');
        /** @var oxCacheBackend $oCacheBackend */
        $oCacheBackend = $this->_getCacheBackend();
        foreach ($aArticleIds as $sArticleId) {
            $oArticle->setId($sArticleId);

            if ($oCacheBackend->isActive()) {
                $oCacheBackend->invalidate($oArticle->getCacheKeys());
            }
        }
    }

    /**
     * Returns cache backend.
     *
     * @return oxCacheBackend
     */
    protected function _getCacheBackend()
    {
        return $this->_oCacheBackend;
    }

    /**
     * Returns oeVATTBEArticleVATGroupsList
     * Used to get articles assigned to specific group.
     *
     * @return oeVATTBEArticleVATGroupsList
     */
    protected function _getArticleVATGroupsList()
    {
        return $this->_oArticleVATGroupsList;
    }
}
