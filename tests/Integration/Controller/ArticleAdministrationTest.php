<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Controller;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\EVatModule\Controller\Admin\ArticleAdministration;
use OxidEsales\Facts\Facts;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Testing VAT TBE administration in article page.
 */
class ArticleAdministrationTest extends TestCase
{
    /**
     * Test if render set readonly mode for subshops.
     *
     * @return true|null
     */
    public function testRenderSetsReadOnlyModeForSubshops()
    {
        if ('EE' != (new Facts())->getEdition()) {
            $this->markTestSkipped('Test only on Enterprise shop');
        }

        /** @var Article|MockObject oxArticle */
        $oDerivedArticle = $this->createPartialMock(Article::class, ['isDerived']);
        $oDerivedArticle->expects($this->any())->method('isDerived')->will($this->returnValue(true));

        /** @var ArticleAdministration|MockObject $oArticleAdministration */
        $oArticleAdministration = $this->createPartialMock(ArticleAdministration::class, ['loadCurrentArticle']);
        $oArticleAdministration->expects($this->atLeastOnce())->method('loadCurrentArticle')->will($this->returnValue($oDerivedArticle));
        $oArticleAdministration->render();

        $aViewData = $oArticleAdministration->getViewData();
        $this->assertTrue($aViewData['readonly'], 'View data contains: ' . serialize($aViewData));
    }

    /**
     * Test if render do not set readonly mode for main shop.
     *
     * @return true|null
     */
    public function testRenderDoNotSetsReadOnlyModeForMainShop()
    {
        if ('EE' != (new Facts())->getEdition()) {
            $this->markTestSkipped('Test only on Enterprise shop');
        }

        /** @var Article|MockObject oxArticle */
        $oDerivedArticle = $this->createPartialMock(Article::class, ['isDerived']);
        $oDerivedArticle->expects($this->any())->method('isDerived')->will($this->returnValue(false));

        /** @var ArticleAdministration|MockObject $oArticleAdministration */
        $oArticleAdministration = $this->createPartialMock(ArticleAdministration::class, ['loadCurrentArticle']);
        $oArticleAdministration->expects($this->atLeastOnce())->method('loadCurrentArticle')->will($this->returnValue($oDerivedArticle));
        $oArticleAdministration->render();

        $aViewData = $oArticleAdministration->getViewData();
        $this->assertTrue(!isset($aViewData['readonly']), 'View data contains: ' . serialize($aViewData));
    }

    /**
     * Test if render set readonly mode for subshops.
     *
     * @return true|null
     */
    public function testRenderDoesNotSetsReadOnlyModeForDifferentEditions()
    {
        if ('EE' == (new Facts())->getEdition()) {
            $this->markTestSkipped('Test only on Enterprise shop');
        }

        /** @var Article|MockObject oxArticle */
        $oDerivedArticle = $this->createPartialMock(Article::class, ['isDerived']);
        $oDerivedArticle->expects($this->any())->method('isDerived')->will($this->returnValue(true));

        /** @var ArticleAdministration|MockObject $oArticleAdministration */
        $oArticleAdministration = $this->createPartialMock(ArticleAdministration::class, ['loadCurrentArticle']);
        $oArticleAdministration->expects($this->atLeastOnce())->method('loadCurrentArticle')->will($this->returnValue($oDerivedArticle));
        $oArticleAdministration->render();

        $aViewData = $oArticleAdministration->getViewData();
        $this->assertTrue(!isset($aViewData['readonly']), 'View data contains: ' . serialize($aViewData));
    }
}
