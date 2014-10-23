<?php
/**
 * This file is part of OXID eSales VAT TBE module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales VAT TBE module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */


/**
 * Testing oeVATTBECountryVATGroupsList class.
 *
 * @covers oeVATTBECountryVATGroupsList
 * @covers oeVATTBECountryVATGroup
 * @covers oeVATTBECountryVATGroupsDbGateway
 */
class Integration_oeVatTbe_VATGroups_oeVATTBECountryVATGroupsListTest extends OxidTestCase
{
    /**
     * Tests saving group to database.
     *
     * @return string
     */
    public function testSavingGroup()
    {
        $oGroup = oeVATTBECountryVATGroup::createCountryVATGroup();

        $oGroup->setCountryId('8f241f11095410f38.37165361');
        $oGroup->setName('Group Name');
        $oGroup->setDescription('Some description');
        $oGroup->setRate('20.50');

        $sGroupId = $oGroup->save();

        $oExpectedGroup = oeVATTBECountryVATGroup::createCountryVATGroup();
        $oExpectedGroup->load($sGroupId);

        $this->assertEquals($oGroup->getCountryId(), $oExpectedGroup->getCountryId());
        $this->assertEquals($oGroup->getName(), $oExpectedGroup->getName());
        $this->assertEquals($oGroup->getDescription(), $oExpectedGroup->getDescription());
        $this->assertEquals($oGroup->getRate(), $oExpectedGroup->getRate());

        return $sGroupId;
    }
    /**
     * Tests updating group without loading it, but providing its id and all info instead.
     *
     * @param string $sGroupId
     *
     * @depends testSavingGroup
     */
    public function testUpdatingGroupWithoutLoadingIt($sGroupId)
    {
        $oGroup = oeVATTBECountryVATGroup::createCountryVATGroup();

        $oGroup->setId($sGroupId);
        $oGroup->setCountryId('8f241f11095410f38.37165361');
        $oGroup->setName('New Group Name');
        $oGroup->setDescription('New description');
        $oGroup->setRate('20.60');

        $oGroup->save();

        $oExpectedGroup = oeVATTBECountryVATGroup::createCountryVATGroup();
        $oExpectedGroup->load($sGroupId);

        $this->assertEquals($oGroup->getCountryId(), $oExpectedGroup->getCountryId());
        $this->assertEquals($oGroup->getName(), $oExpectedGroup->getName());
        $this->assertEquals($oGroup->getDescription(), $oExpectedGroup->getDescription());
        $this->assertEquals($oGroup->getRate(), $oExpectedGroup->getRate());
    }
}
