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
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopEnterprise\Core\Cache\Generic\Cache;
use OxidEsales\EVatModule\Core\oeVATTBEModel;

/**
 * VAT Groups handling class
 */
class oeVATTBEVATGroupArticleCacheInvalidator extends oeVATTBEModel
{
    /** @var Cache Cache backend. */
    private $_oCacheBackend = null;

    /** @var oeVATTBEArticleVATGroupsList Used to get articles assigned to specific group. */
    private $_oArticleVATGroupsList = null;

    /**
     * Handles class dependencies.
     *
     * @param oeVATTBEArticleVATGroupsList $oArticleVATGroupsList Used to get articles assigned to specific group.
     * @param Cache               $oCacheBackend         Cache backend
     */
    public function __construct(oeVATTBEArticleVATGroupsList $oArticleVATGroupsList, Cache $oCacheBackend)
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
        $oCacheBackend = Registry::get(\OxidEsales\Eshop\Core\Cache\Generic\Cache::class);
        $ArticleGroupsList = oeVATTBEArticleVATGroupsList::createInstance();

        return oxNew(oeVATTBEVATGroupArticleCacheInvalidator::class, $ArticleGroupsList, $oCacheBackend);
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

        /** @var Article $oArticle */
        $oArticle = oxNew(Article::class);
        /** @var Cache $oCacheBackend */
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
     * @return Cache
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
