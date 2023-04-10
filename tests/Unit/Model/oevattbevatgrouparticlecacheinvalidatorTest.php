<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\VisualCmsModule\Tests\Unit\Model;

include_once __DIR__ . '/../../../libs/oxtestcacheconnector.php';

use PHPUnit\Framework\TestCase;

/**
 * Test class for oeVATTBEVATGroupArticleCacheInvalidator.
 *
 * @covers oeVATTBEVATGroupArticleCacheInvalidator
 */
class Unit_oeVATTBE_Models_oeVATTBEVATGroupArticleCacheInvalidatorTest extends TestCase
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
