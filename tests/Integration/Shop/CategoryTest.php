<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Shop;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\EVatModule\Shop\Category;
use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxCategory class.
 */
class CategoryTest extends TestCase
{
    /**
     * Test if getter for data field works correct.
     */
    public function testIsOEVATTBETBE()
    {
        $oCategory = oxNew(Category::class);
        $oCategory->oxcategories__oevattbe_istbe = new Field(1);
        $this->assertTrue($oCategory->isOeVATTBETBE());

        $oCategory->oxcategories__oevattbe_istbe = new Field(0);
        $this->assertFalse($oCategory->isOeVATTBETBE());
    }
}
