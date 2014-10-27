<?php
/**
 * This file is part of OXID eSales VAT TBE module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales VAT TBE module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014T
 */

/**
 * Test class for oeVATTBEVATGroupArticleCacheInvalidator.
 *
 * @covers oeVATTBEVATGroupArticleCacheInvalidator
 */
class Unit_oeVATTBE_Models_oeVATTBEVATGroupArticleCacheInvalidatorTest extends OxidTestCase
{
    /**
     * Test if error message is formed correctly.
     */
    public function testArticleInvalidation()
    {
        if ($this->getConfig()->getEdition() != 'EE') {
            $this->markTestSkipped('Test only on Enterprise shop');
        }
        $this->getConfig()->setConfigParam('blCacheActive', true);

        $aMethods = array('getArticlesAssignedToGroup' => array('article1', 'article2'));
        $oArticleVATGroupsList = $this->_createStub('oeVATTBEArticleVATGroupsList', $aMethods);

        $oConnector = new oxTestCacheConnector();
        $oConnector->set('oxArticle_article1_1_en', 1);
        $oConnector->set('oxArticle_article2_1_en', 1);
        $oConnector->set('oxArticle_article3_1_en', 1);

        /** @var oxCacheBackend $oCacheBackend */
        $oCacheBackend = oxNew('oxCacheBackend');
        $oCacheBackend->registerConnector($oConnector);

        $oInvalidator = oxNew('oeVATTBEVATGroupArticleCacheInvalidator', $oArticleVATGroupsList, $oCacheBackend);
        $oInvalidator->invalidate('groupId');

        $this->assertEquals(array('oxArticle_article3_1_en' => 1), $oConnector->aCache);
    }
}

if (oxRegistry::getConfig()->getEdition() === 'EE') {
    /**
     * Mock connector class for CacheBackend invalidation testing.
     */
    class oxTestCacheConnector implements oxiCacheConnector
    {
        /** @var array */
        public $aCache = array();

        /**
         * Returns that cache is always active.
         *
         * @return bool
         */
        public static function isAvailable()
        {
            return true;
        }

        /**
         * Sets value to cache.
         *
         * @param string|array $mKey
         * @param mixed        $mValue
         * @param int          $iTTL
         */
        public function set($mKey, $mValue = null, $iTTL = null)
        {
            if (is_array($mKey)) {
                $this->aCache = array_merge($this->aCache, $mKey);
            } else {
                $this->aCache[$mKey] = $mValue;
            }
        }

        /**
         * Returns value in cache.
         *
         * @param array|string $mKey
         *
         * @return array
         */
        public function get($mKey)
        {
            if (is_array($mKey)) {
                return array_intersect_key($this->aCache, array_flip($mKey));
            } else {
                return $this->aCache[$mKey];
            }
        }

        /**
         * Removes item with given key from cache.
         *
         * @param array|string $mKey
         */
        public function invalidate($mKey)
        {
            if (is_array($mKey)) {
                $this->aCache = array_diff_key($this->aCache, array_flip($mKey));
            } else {
                $this->aCache[$mKey] = null;
            }
        }

        /**
         * Removes all items from cache.
         */
        public function flush()
        {
            $this->aCache = array();
        }
    }
}
