<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\EshopEnterprise\Core\Cache\Generic\Cache;

/**
 * VAT Groups handling class
 */
class GroupArticleCacheInvalidator
{
    /** @var Cache Cache backend. */
    private $_oCacheBackend = null;

    /** @var ArticleVATGroupsList Used to get articles assigned to specific group. */
    private $_oArticleVATGroupsList = null;

    /**
     * Handles class dependencies.
     *
     * @param ArticleVATGroupsList $oArticleVATGroupsList Used to get articles assigned to specific group.
     * @param Cache               $oCacheBackend         Cache backend
     */
    public function __construct(ArticleVATGroupsList $oArticleVATGroupsList, Cache $oCacheBackend)
    {
        $this->_oArticleVATGroupsList = $oArticleVATGroupsList;
        $this->_oCacheBackend = $oCacheBackend;
    }

    /**
     * Clears cache for VAT group articles.
     *
     * @param string $sGroupId
     */
    public function invalidate($sGroupId)
    {
        $oArticleVATGroupsList = $this->getArticleVATGroupsList();
        $aArticleIds = $oArticleVATGroupsList->getArticlesAssignedToGroup($sGroupId);

        /** @var Article $oArticle */
        $oArticle = oxNew(Article::class);
        /** @var Cache $oCacheBackend */
        $oCacheBackend = $this->getCacheBackend();
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
     * @return Cache
     */
    protected function getCacheBackend()
    {
        return $this->_oCacheBackend;
    }

    /**
     * Returns oeVATTBEArticleVATGroupsList
     * Used to get articles assigned to specific group.
     *
     * @return ArticleVATGroupsList
     */
    protected function getArticleVATGroupsList()
    {
        return $this->_oArticleVATGroupsList;
    }
}
