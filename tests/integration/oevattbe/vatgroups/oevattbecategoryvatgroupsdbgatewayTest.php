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
 * Test class for oeVATTBECategoryVATGroupsDbGateway.
 *
 * @covers oeVATTBECategoryVATGroupsDbGateway
 */
class Integration_oeVatTbe_VATGroups_oeVATTBECategoryVATGroupsDbGatewayTest extends OxidTestCase
{
    /**
     * Testing VAT Group saving to database.
     *
     * @return string Category id, for which record was saved.
     */
    public function testSavingVATGroupToDatabase()
    {
        /** @var oeVATTBECategoryVATGroupsDbGateway $oVatGroupsGateway */
        $oVatGroupsGateway = oxNew('oeVATTBECategoryVATGroupsDbGateway');
        $aData = array(
            'categoryid' => '0962081a5693597654fd2887af7a6095',
            'relations' => array(
                array(
                    'OEVATTBE_CATEGORYID' => '0962081a5693597654fd2887af7a6095',
                    'OEVATTBE_COUNTRYID' => 'a7c40f631fc920687.20179984',
                    'OEVATTBE_VATGROUPID' => '10',
                ),
            ),
        );
        $this->assertTrue($oVatGroupsGateway->save($aData));

        return '0962081a5693597654fd2887af7a6095';
    }

    /**
     * Testing VAT Group updating.
     *
     * @param string $sCategoryId Category id, for which group was created.
     *
     * @depends testSavingVATGroupToDatabase
     *
     * @return string Category id, for which record was saved.
     */
    public function testUpdatingVATGroupToDatabase($sCategoryId)
    {
        /** @var oeVATTBECategoryVATGroupsDbGateway $oVatGroupsGateway */
        $oVatGroupsGateway = oxNew('oeVATTBECategoryVATGroupsDbGateway');
        $aData = array(
            'categoryid' => '0962081a5693597654fd2887af7a6095',
            'relations' => array(
                array(
                    'OEVATTBE_CATEGORYID' => $sCategoryId,
                    'OEVATTBE_COUNTRYID' => 'a7c40f631fc920687.20179984',
                    'OEVATTBE_VATGROUPID' => '11',
                ),
                array(
                    'OEVATTBE_CATEGORYID' => $sCategoryId,
                    'OEVATTBE_COUNTRYID' => '8f241f110958b69e4.93886171',
                    'OEVATTBE_VATGROUPID' => '12',
                ),
            ),
        );
        $this->assertTrue($oVatGroupsGateway->save($aData));

        return $sCategoryId;
    }

    /**
     * Testing VAT Group loading from database.
     *
     * @param string $sCategoryId Category id, for which group was created.
     *
     * @depends testUpdatingVATGroupToDatabase
     *
     * @return string
     */
    public function testVATGroupLoading($sCategoryId)
    {
        $oVatGroupsGateway = oxNew('oeVATTBECategoryVATGroupsDbGateway');
        $aData = $oVatGroupsGateway->load($sCategoryId);

        $aExpectedData = array(
            array(
                'OEVATTBE_CATEGORYID' => $sCategoryId,
                'OEVATTBE_COUNTRYID' => '8f241f110958b69e4.93886171',
                'OEVATTBE_VATGROUPID' => '12',
                'OEVATTBE_TIMESTAMP' => $aData[1]['OEVATTBE_TIMESTAMP'],
            ),
            array(
                'OEVATTBE_CATEGORYID' => $sCategoryId,
                'OEVATTBE_COUNTRYID' => 'a7c40f631fc920687.20179984',
                'OEVATTBE_VATGROUPID' => '11',
                'OEVATTBE_TIMESTAMP' => $aData[0]['OEVATTBE_TIMESTAMP'],
            )
        );

        $this->assertSame($aExpectedData, $aData);

        return $sCategoryId;
    }

    /**
     * Testing VAT Group loading from database by group id.
     *
     * @param string $sCategoryId Category id, for which group was created.
     *
     * @depends testVATGroupLoading
     *
     * @return string
     */
    public function testVATGroupLoadingByGroupId($sCategoryId)
    {
        /** @var oeVATTBECategoryVATGroupsDbGateway $oVatGroupsGateway */
        $oVatGroupsGateway = oxNew('oeVATTBECategoryVATGroupsDbGateway');
        $aData = $oVatGroupsGateway->loadByGroupId('12');

        $aExpectedData = array(
            array(
                'OEVATTBE_CATEGORYID' => $sCategoryId,
                'OEVATTBE_COUNTRYID' => '8f241f110958b69e4.93886171',
                'OEVATTBE_VATGROUPID' => '12',
                'OEVATTBE_TIMESTAMP' => $aData[0]['OEVATTBE_TIMESTAMP'],
            ),
        );

        $this->assertSame($aExpectedData, $aData);

        return $sCategoryId;
    }

    /**
     * Testing deletion of VAT Group from database.
     *
     * @param string $sCategoryId category id
     *
     * @depends testVATGroupLoadingByGroupId
     */
    public function testDeletingVATGroupList($sCategoryId)
    {
        $oVatGroupsGateway = oxNew('oeVATTBECategoryVATGroupsDbGateway');
        $oVatGroupsGateway->delete($sCategoryId);

        $this->assertSame(array(), $oVatGroupsGateway->load($sCategoryId));
    }

    /**
     * Test loading non existing VAT Group.
     */
    public function testLoadingEmptyVATGroup()
    {
        $oVatGroupsGateway = oxNew('oeVATTBECategoryVATGroupsDbGateway');
        $this->assertSame(array(), $oVatGroupsGateway->load('non_existing_group'));
    }

    /**
     * Test deleting non existing VAT Group.
     */
    public function testDeletingEmptyVATGroup()
    {
        $oVatGroupsGateway = oxNew('oeVATTBECategoryVATGroupsDbGateway');
        $this->assertNotSame(false, $oVatGroupsGateway->delete('non_existing_group'));
    }
}
