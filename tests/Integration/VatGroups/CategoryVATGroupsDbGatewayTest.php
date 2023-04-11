<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\VatGroups;

use PHPUnit\Framework\TestCase;

/**
 * Test class for CategoryVATGroupsDbGateway.
 *
 * @covers CategoryVATGroupsDbGateway
 */
class CategoryVATGroupsDbGatewayTest extends TestCase
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
