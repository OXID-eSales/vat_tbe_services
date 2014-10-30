<?php
/**
 * This file is part of OXID eSales eVAT module.
 *
 * OXID eSales eVAT module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales eVAT module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales eVAT module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */


/**
 * Testing extended oxCountry class.
 *
 * @covers oeVATTBEOxCountry
 */
class Unit_oeVATTBE_models_oeVATTBEOxCountryTest extends OxidTestCase
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
