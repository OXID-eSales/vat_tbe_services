<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Shop;

use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxArticle class.
 *
 * @covers BasketContentMarkGenerator
 */
class BasketContentMarkGeneratorTest extends TestCase
{
    /**
     * Mark calculation test case: just tbe
     */
    public function testGetMark()
    {
        $oBasket = $this->getMock('oeVATTBEOxBasket', array('hasOeTBEVATArticles'));
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));

        $oGenerator = oxNew('oxBasketContentMarkGenerator', $oBasket);

        $this->assertSame('**', $oGenerator->getMark('tbeService'));
    }

    /**
     * Mark calculation test case: tbe and other
     */
    public function testGetMarkHasOtherMarks()
    {
        $oBasket = $this->getMock('oeVATTBEOxBasket', array('hasOeTBEVATArticles', 'hasArticlesWithDownloadableAgreement'));
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));
        $oBasket->expects($this->any())->method('hasArticlesWithDownloadableAgreement')->will($this->returnValue(true));

        $oGenerator = oxNew('oxBasketContentMarkGenerator', $oBasket);

        $this->assertSame('**', $oGenerator->getMark('tbeService'));
        $this->assertSame('***', $oGenerator->getMark('downloadable'));
    }

    /**
     * Mark calculation test case: no tbe
     */
    public function testGetMarkHasOtherMarksButNotTBE()
    {
        $oBasket = $this->getMock('oeVATTBEOxBasket', array('hasOeTBEVATArticles', 'hasArticlesWithDownloadableAgreement'));
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(false));
        $oBasket->expects($this->any())->method('hasArticlesWithDownloadableAgreement')->will($this->returnValue(true));

        $oGenerator = oxNew('oxBasketContentMarkGenerator', $oBasket);

        $this->assertSame(null, $oGenerator->getMark('tbeService'));
        $this->assertSame('**', $oGenerator->getMark('downloadable'));
    }
}
