<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Shop;

use OxidEsales\Eshop\Core\Field;
use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxArticle class.
 *
 * @covers Article
 * @covers ArticleCacheKey
 */
class ArticleTest extends TestCase
{
    /**
     * Test for vat tbe getter
     */
    public function testTbeVatGetter()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oevattbe_rate = new Field(9);
        $this->assertSame(9, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Test for is tbe service
     */
    public function testisOeVATTBETBEService()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oevattbe_istbeservice = new oxField(false);
        $this->assertFalse($oArticle->isOeVATTBETBEService());
    }
}
