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

include_once __DIR__ . '/../../../libs/oxtestcacheconnector.php';

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
