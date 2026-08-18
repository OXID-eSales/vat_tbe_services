<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Shop;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

/**
 * Testing extended oxArticle class.
 */
class ArticleTest extends IntegrationTestCase
{
    /**
     * Test for vat tbe getter
     */
    public function testTbeVatGetter()
    {
        $oArticle = oxNew(Article::class);
        $oArticle->oxarticles__oevattbe_rate = new Field(9);
        $this->assertSame(9, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Test for is tbe service
     */
    public function testisOeVATTBETBEService()
    {
        $oArticle = oxNew(Article::class);
        $oArticle->oxarticles__oevattbe_istbeservice = new Field(false);
        $this->assertFalse($oArticle->isOeVATTBETBEService());
    }
}
