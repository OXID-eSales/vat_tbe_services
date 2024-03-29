<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Model;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Cache\Generic\Cache;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\ArticleVATGroupsList;
use OxidEsales\EVatModule\Model\GroupArticleCacheInvalidator;
use OxidEsales\EVatModule\Tests\Libs\TestCacheConnector;
use OxidEsales\Facts\Facts;
use PHPUnit\Framework\TestCase;

/**
 * Test class for oeVATTBEVATGroupArticleCacheInvalidator.
 */
class VATGroupArticleCacheInvalidatorTest extends TestCase
{
    /**
     * Test if error message is formed correctly.
     */
    public function testArticleInvalidation()
    {
        if ((new Facts())->getEdition() != 'EE') {
            $this->markTestSkipped('Test only on Enterprise shop');
        }
        Registry::getConfig()->setConfigParam('blCacheActive', true);

        $oArticleVATGroupsList = $this->createStub(ArticleVATGroupsList::class);
        $oArticleVATGroupsList
            ->method('getArticlesAssignedToGroup')
            ->willReturn(['article1', 'article2']);

        Registry::getSession()->setUser(oxNew(User::class));

        $oConnector = new TestCacheConnector();
        $oConnector->set('oxArticle_article1_1_en', 1);
        $oConnector->set('oxArticle_article2_1_en', 1);
        $oConnector->set('oxArticle_article3_1_en', 1);

        /** @var Cache $oCacheBackend */
        $oCacheBackend = oxNew(Cache::class);
        $oCacheBackend->registerConnector($oConnector);

        $oInvalidator = oxNew(GroupArticleCacheInvalidator::class, $oArticleVATGroupsList, $oCacheBackend);
        $oInvalidator->invalidate('groupId');

        $this->assertEquals(['oxArticle_article3_1_en' => 1], $oConnector->aCache);
    }
}
