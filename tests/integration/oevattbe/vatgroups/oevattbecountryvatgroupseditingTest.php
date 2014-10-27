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
 * Testing oeVATTBECountryVatGroups class.
 *
 * @covers oeVATTBECountryVatGroups
 * @covers oeVATTBECountryVATGroupsDbGateway
 * @covers oeVATTBECountryVATGroup
 * @covers oeVATTBECountryVATGroupsList
 */
class Integration_oeVatTbe_VATGroups_oeVATTBECountryVATGroupsEditingTest extends OxidTestCase
{
    /**
     * Gives test cases to get country groups
     * Provides country to load and groups which should exist in that country.
     *
     * @return array
     */
    public function providerGetVatGroupsForCountry()
    {
        $sAustriaId = 'a7c40f6320aeb2ec2.72885259';
        $aAustriaGroups = array(56, 57, 58);

        $sGermanyId = 'a7c40f631fc920687.20179984';
        $aGermanyGroups = array(10, 11);

        return array(
            array($sAustriaId, $aAustriaGroups),
            array($sGermanyId, $aGermanyGroups),
        );
    }

    /**
     * Test if getting VAT groups for Country works.
     *
     * @param string $sCountryId      country id to load groups.
     * @param array  $aExpectedGroups groups which should exist in given country.
     *
     * @dataProvider providerGetVatGroupsForCountry
     */
    public function testGetVatGroupsForCountry($sCountryId, $aExpectedGroups)
    {
        /** @var oeVATTBECountryVatGroups $oVATTBECountryVatGroups */
        $oVATTBECountryVatGroups = oxNew('oeVATTBECountryVatGroups');
        $oVATTBECountryVatGroups->setEditObjectId($sCountryId);
        $aCountryVatGroups = $oVATTBECountryVatGroups->getVatGroups();

        /** @var oeVATTBECountryVATGroup $aCountryVatGroup */
        foreach ($aCountryVatGroups as $aCountryVatGroup) {
            $this->assertInstanceOf('oeVATTBECountryVATGroup', $aCountryVatGroup);
            $this->assertArrayHasValue($aCountryVatGroup->getId(), $aExpectedGroups);
            $this->assertSame(
                count($aExpectedGroups),
                count($aCountryVatGroups),
                'Groups count did not match expected: '. serialize($aExpectedGroups)
                .' actual: '. serialize($aCountryVatGroups)
            );
        }
    }

    /**
     * Asserts that an array has a specified key.
     *
     * @param mixed             $value   value to search in array.
     * @param array|ArrayAccess $array   array which should contain value.
     * @param string            $message failure message.
     */
    public function assertArrayHasValue($value, $array, $message = '')
    {
        $arrangedArray = array_flip($array);
        $this->assertArrayHasKey($value, $arrangedArray, $message);
    }
}
