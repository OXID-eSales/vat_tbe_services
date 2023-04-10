<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Shop;

use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxCategory class.
 *
 * @covers Category
 */
class CategoryTest extends TestCase
{
    /**
     * Test if getter for data field works correct.
     */
    public function testIsOEVATTBETBE()
    {
        $oCategory = oxNew('oxCategory');
        $oCategory->oxcategories__oevattbe_istbe = new oxField(1);
        $this->assertTrue($oCategory->isOeVATTBETBE());

        $oCategory->oxcategories__oevattbe_istbe = new oxField(0);
        $this->assertFalse($oCategory->isOeVATTBETBE());
    }
}
