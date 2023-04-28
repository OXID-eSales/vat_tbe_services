<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Controller;

use OxidEsales\Eshop\Core\Registry;
use PHPUnit\Framework\TestCase;

/**
 * Testing VAT TBE administration in article page.
 *
 * @covers ArticleAdministration
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
        if ('EE' != Registry::getConfig()->getEdition()) {
            $this->markTestSkipped('Test only on Enterprise shop');
        }

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject oxArticle */
        $oDerivedArticle = $this->getMock('oxArticle', array(), array(), '', false);
        $oDerivedArticle->expects($this->any())->method('isDerived')->will($this->returnValue(true));

        /** @var oeVATTBEArticleAdministration|PHPUnit_Framework_MockObject_MockObject $oArticleAdministration */
        $oArticleAdministration = $this->getMock('oeVATTBEArticleAdministration', array('_loadCurrentArticle'));
        $oArticleAdministration->expects($this->atLeastOnce())->method('_loadCurrentArticle')->will($this->returnValue($oDerivedArticle));
        $oArticleAdministration->render();

        $aViewData = $oArticleAdministration->getViewData();
        $this->assertTrue($aViewData['readonly'], 'View data contains: '. serialize($aViewData));
    }

    /**
     * Test if render do not set readonly mode for main shop.
     *
     * @return true|null
     */
    public function testRenderDoNotSetsReadOnlyModeForMainShop()
    {
        if ('EE' != Registry::getConfig()->getEdition()) {
            $this->markTestSkipped('Test only on Enterprise shop');
        }

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject oxArticle */
        $oDerivedArticle = $this->getMock('oxArticle', array(), array(), '', false);
        $oDerivedArticle->expects($this->any())->method('isDerived')->will($this->returnValue(false));

        /** @var oeVATTBEArticleAdministration|PHPUnit_Framework_MockObject_MockObject $oArticleAdministration */
        $oArticleAdministration = $this->getMock('oeVATTBEArticleAdministration', array('_loadCurrentArticle'));
        $oArticleAdministration->expects($this->atLeastOnce())->method('_loadCurrentArticle')->will($this->returnValue($oDerivedArticle));
        $oArticleAdministration->render();

        $aViewData = $oArticleAdministration->getViewData();
        $this->assertTrue(!isset($aViewData['readonly']), 'View data contains: '. serialize($aViewData));
    }

    /**
     * Test if render set readonly mode for subshops.
     *
     * @return true|null
     */
    public function testRenderDoesNotSetsReadOnlyModeForDifferentEditions()
    {
        if ('EE' == Registry::getConfig()->getEdition()) {
            $this->markTestSkipped('Test only on Enterprise shop');
        }

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject oxArticle */
        $oDerivedArticle = $this->getMock('oxArticle', array(), array(), '', false);
        $oDerivedArticle->expects($this->any())->method('isDerived')->will($this->returnValue(true));

        /** @var oeVATTBEArticleAdministration|PHPUnit_Framework_MockObject_MockObject $oArticleAdministration */
        $oArticleAdministration = $this->getMock('oeVATTBEArticleAdministration', array('_loadCurrentArticle'));
        $oArticleAdministration->expects($this->atLeastOnce())->method('_loadCurrentArticle')->will($this->returnValue($oDerivedArticle));
        $oArticleAdministration->render();

        $aViewData = $oArticleAdministration->getViewData();
        $this->assertTrue(!isset($aViewData['readonly']), 'View data contains: '. serialize($aViewData));
    }
}
