<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\VisualCmsModule\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxCountry class.
 *
 * @covers oeVATTBEOxCountry
 */
class Unit_oeVATTBE_models_oeVATTBEOxCountryTest extends TestCase
{
    /**
     * Test for vat tbe getter
     */
    public function testAppliesOeTBEVATTbeVat()
    {
        $oCountry = oxNew('oxCountry');
        $oCountry->oxcountry__oevattbe_appliestbevat = new oxField(1);
        $this->assertTrue($oCountry->appliesOeTBEVATTbeVat());

        $oCountry->oxcountry__oevattbe_appliestbevat = new oxField(0);
        $this->assertFalse($oCountry->appliesOeTBEVATTbeVat());
    }

    /**
     * Test if getter for data field works correct.
     */
    public function testIsOEVATTBEAtLeastOneGroupConfigured()
    {
        $oCountry = oxNew('oxCountry');
        $oCountry->oxcountry__oevattbe_istbevatconfigured = new oxField(1);
        $this->assertTrue($oCountry->isOEVATTBEAtLeastOneGroupConfigured());

        $oCountry->oxcountry__oevattbe_istbevatconfigured = new oxField(0);
        $this->assertFalse($oCountry->isOEVATTBEAtLeastOneGroupConfigured());
    }

    /**
     * Test for vat tbe getter
     */
    public function testGetOeVATTBEName()
    {
        $oCountry = oxNew('oxCountry');
        $oCountry->oxcountry__oxtitle = new oxField('LT');
        $this->assertSame('LT', $oCountry->getOeVATTBEName());
    }
}
