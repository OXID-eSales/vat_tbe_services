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
 * Test class for oeVATTBECountryVATGroupsDbGateway.
 *
 * @covers oeVATTBECountryVATGroupsDbGateway
 */
class Integration_oeVatTbe_VATGroups_oeVATTBECountryVATGroupsDbGatewayTest extends OxidTestCase
{

    /**
     * Testing VAT Group saving to database.
     *
     * @return string
     */
    public function testSavingVATGroupToDatabase()
    {
        $oVatGroupsGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        $aData = array(
            'oevattbe_countryid' => '8f241f11095410f38.37165361',
            'oevattbe_name' => 'Group Name',
            'oevattbe_description' => 'Some description',
            'oevattbe_rate' => 20.50
        );
        $sGroupId = $oVatGroupsGateway->save($aData);
        $this->assertTrue(is_string($sGroupId));

        return $sGroupId;
    }

    /**
     * Testing VAT Group loading from database.
     *
     * @param string $sGroupId
     *
     * @depends testSavingVATGroupToDatabase
     *
     * @return string
     */
    public function testVATGroupLoading($sGroupId)
    {
        $oVatGroupsGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        $aData = $oVatGroupsGateway->load($sGroupId);

        $aExpectedData = array(
            'OEVATTBE_ID' => $sGroupId,
            'OEVATTBE_COUNTRYID' => '8f241f11095410f38.37165361',
            'OEVATTBE_NAME' => 'Group Name',
            'OEVATTBE_DESCRIPTION' => 'Some description',
            'OEVATTBE_RATE' => '20.50',
            'OEVATTBE_TIMESTAMP' => $aData['OEVATTBE_TIMESTAMP'],
        );

        $this->assertSame($aExpectedData, $aData);

        return $sGroupId;
    }

    /**
     * Testing deletion of VAT Group from database.
     *
     * @param string $sGroupId
     *
     * @depends testVATGroupLoading
     */
    public function testDeletingVATGroupList($sGroupId)
    {
        $oVatGroupsGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        $oVatGroupsGateway->delete($sGroupId);

        $this->assertSame(array(), $oVatGroupsGateway->load($sGroupId));
    }

    /**
     * Testing deletion of article and groups dependencies on group deletion.
     */
    public function testDeletingVATGroupAndArticlesRelations()
    {
        $oRelationsList = oeVATTBEArticleVATGroupsList::createArticleVATGroupsList();
        $oRelationsList->setId('articleid');
        $oRelationsList->setData(array('germanyid' => '10', 'lithuaniaid' => '12'));
        $oRelationsList->save();

        $oVatGroupsGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        $oVatGroupsGateway->delete('12');

        $oRelationsList->load('articleid');
        $this->assertSame(array('germanyid' => '10'), $oRelationsList->getData());
    }

    /**
     * Test loading non existing VAT Group.
     */
    public function testLoadingEmptyVATGroup()
    {
        $oVatGroupsGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        $this->assertSame(array(), $oVatGroupsGateway->load('non_existing_group'));
    }

    /**
     * Test deleting non existing VAT Group.
     */
    public function testDeletingEmptyVATGroup()
    {
        $oVatGroupsGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        $this->assertNotSame(false, $oVatGroupsGateway->delete('non_existing_group'));
    }

    /**
     * VAT Groups exists for specific country;
     * Groups list is loaded with correct information for this country.
     */
    public function testLoadingGroupsListForCountryWhenGroupsExist()
    {
        $this->addTableForCleanup('oevattbe_countryvatgroups');

        $oVatGroupsGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        $aData = array(
            'OEVATTBE_COUNTRYID' => '8f241f11095410f38.37165361',
            'OEVATTBE_NAME' => 'Group Name',
            'OEVATTBE_DESCRIPTION' => 'Some description',
            'OEVATTBE_RATE' => 20.50
        );
        $sGroupId1 = $oVatGroupsGateway->save($aData);
        $sGroupId2 = $oVatGroupsGateway->save($aData);

        $oVatGroupsGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        $aGroupsList = $oVatGroupsGateway->getList('8f241f11095410f38.37165361');

        $aData1 = $aData;
        $aData1['OEVATTBE_ID'] = $sGroupId1;
        $aData1['OEVATTBE_TIMESTAMP'] = $aGroupsList[0]['OEVATTBE_TIMESTAMP'];

        $aData2 = $aData;
        $aData2['OEVATTBE_ID'] = $sGroupId2;
        $aData2['OEVATTBE_TIMESTAMP'] = $aGroupsList[1]['OEVATTBE_TIMESTAMP'];

        $this->assertEquals(array($aData1, $aData2), $aGroupsList);
    }

    /**
     * VAT Groups exists for specific country;
     * Groups list is loaded with correct information for this country.
     */
    public function testLoadingGroupsListWhenGroupsExistAndNoCountryIsPassed()
    {
        $oVatGroupsGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        $aGroupsList = $oVatGroupsGateway->getList();

        $this->assertNotEmpty($aGroupsList);
    }

    /**
     * No VAT Groups exists for specific country;
     * Empty groups list should be returned.
     */
    public function testLoadingGroupsListWhenNoGroupsExist()
    {
        $oVatGroupsGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        $aGroupsList = $oVatGroupsGateway->getList('8f241f11095410f38.37165361');

        $this->assertEquals(array(), $aGroupsList);
    }
}
