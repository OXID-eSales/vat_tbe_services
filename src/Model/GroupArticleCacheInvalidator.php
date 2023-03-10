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
use OxidEsales\EshopEnterprise\Core\Cache\Generic\Cache;
use OxidEsales\EVatModule\Core\Model;

/**
 * VAT Groups handling class
 */
class GroupArticleCacheInvalidator extends Model
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
