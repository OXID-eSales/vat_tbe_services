<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\VisualCmsModule\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxCategory class.
 *
 * @covers oeVATTBEOxCategory
 */
class Unit_oeVATTBE_models_oeVATTBEOxCategoryTest extends TestCase
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
