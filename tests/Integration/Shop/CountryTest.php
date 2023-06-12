<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Shop;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\EVatModule\Shop\Country;
use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxCountry class.
 */
class CountryTest extends TestCase
{
    /**
     * Test for vat tbe getter
     */
    public function testAppliesOeTBEVATTbeVat()
    {
        $oCountry = oxNew(Country::class);
        $oCountry->oxcountry__oevattbe_appliestbevat = new Field(1);
        $this->assertTrue($oCountry->appliesOeTBEVATTbeVat());

        $oCountry->oxcountry__oevattbe_appliestbevat = new Field(0);
        $this->assertFalse($oCountry->appliesOeTBEVATTbeVat());
    }

    /**
     * Test if getter for data field works correct.
     */
    public function testIsOEVATTBEAtLeastOneGroupConfigured()
    {
        $oCountry = oxNew(Country::class);
        $oCountry->oxcountry__oevattbe_istbevatconfigured = new Field(1);
        $this->assertTrue($oCountry->isOEVATTBEAtLeastOneGroupConfigured());

        $oCountry->oxcountry__oevattbe_istbevatconfigured = new Field(0);
        $this->assertFalse($oCountry->isOEVATTBEAtLeastOneGroupConfigured());
    }

    /**
     * Test for vat tbe getter
     */
    public function testGetOeVATTBEName()
    {
        $oCountry = oxNew(Country::class);
        $oCountry->oxcountry__oxtitle = new Field('LT');
        $this->assertSame('LT', $oCountry->getOeVATTBEName());
    }
}
