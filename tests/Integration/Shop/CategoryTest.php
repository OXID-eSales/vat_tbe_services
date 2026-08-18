<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Shop;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\EVatModule\Shop\Category;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

/**
 * Testing extended oxCategory class.
 */
class CategoryTest extends IntegrationTestCase
{
    /**
     * Test if getter for data field works correct.
     */
    public function testIsOEVATTBETBE()
    {
        $oCategory = oxNew(Category::class);
        $oCategory->assign([
            'oxparentid'     => '',
            'oevattbe_istbe' => 1
        ]);
        $oCategory->save();
        $this->assertTrue($oCategory->isOeVATTBETBE());

        $oCategory->assign([
            'oevattbe_istbe' => 0
        ]);
        $oCategory->save();
        $this->assertFalse($oCategory->isOeVATTBETBE());
    }
}
