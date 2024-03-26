<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\VatGroups;

use OxidEsales\EVatModule\Model\DbGateway\CategoryVATGroupsDbGateway;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Test class for CategoryVATGroupsDbGateway.
 */
class CategoryVATGroupsDbGatewayTest extends BaseTestCase
{
    /**
     * Testing VAT Group saving to database.
     *
     * @return string Category id, for which record was saved.
     */
    public function testSavingVATGroupToDatabase()
    {
        /** @var CategoryVATGroupsDbGateway $oVatGroupsGateway */
        $oVatGroupsGateway = oxNew(CategoryVATGroupsDbGateway::class);
        $aData = [
            'categoryid' => '0962081a5693597654fd2887af7a6095',
            'relations'  => [
                [
                    'OEVATTBE_CATEGORYID' => '0962081a5693597654fd2887af7a6095',
                    'OEVATTBE_COUNTRYID'  => 'a7c40f631fc920687.20179984',
                    'OEVATTBE_VATGROUPID' => 10,
                ],
            ],
        ];
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
        /** @var CategoryVATGroupsDbGateway $oVatGroupsGateway */
        $oVatGroupsGateway = oxNew(CategoryVATGroupsDbGateway::class);
        $aData = [
            'categoryid' => '0962081a5693597654fd2887af7a6095',
            'relations'  => [
                [
                    'OEVATTBE_CATEGORYID' => $sCategoryId,
                    'OEVATTBE_COUNTRYID'  => 'a7c40f631fc920687.20179984',
                    'OEVATTBE_VATGROUPID' => 11,
                ],
                [
                    'OEVATTBE_CATEGORYID' => $sCategoryId,
                    'OEVATTBE_COUNTRYID'  => '8f241f110958b69e4.93886171',
                    'OEVATTBE_VATGROUPID' => 12,
                ],
            ],
        ];
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
        $oVatGroupsGateway = oxNew(CategoryVATGroupsDbGateway::class);
        $aData = $oVatGroupsGateway->load($sCategoryId);

        $aExpectedData = [
            [
                'OEVATTBE_CATEGORYID' => $sCategoryId,
                'OEVATTBE_COUNTRYID'  => '8f241f110958b69e4.93886171',
                'OEVATTBE_VATGROUPID' => 12,
                'OEVATTBE_TIMESTAMP'  => $aData[1]['OEVATTBE_TIMESTAMP'],
            ],
            [
                'OEVATTBE_CATEGORYID' => $sCategoryId,
                'OEVATTBE_COUNTRYID'  => 'a7c40f631fc920687.20179984',
                'OEVATTBE_VATGROUPID' => 11,
                'OEVATTBE_TIMESTAMP'  => $aData[0]['OEVATTBE_TIMESTAMP'],
            ]
        ];

        $this->assertEquals($aExpectedData, $aData);

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
        /** @var CategoryVATGroupsDbGateway $oVatGroupsGateway */
        $oVatGroupsGateway = oxNew(CategoryVATGroupsDbGateway::class);
        $aData = $oVatGroupsGateway->loadByGroupId('12');

        $aExpectedData = [
            [
                'OEVATTBE_CATEGORYID' => $sCategoryId,
                'OEVATTBE_COUNTRYID'  => '8f241f110958b69e4.93886171',
                'OEVATTBE_VATGROUPID' => 12,
                'OEVATTBE_TIMESTAMP'  => $aData[0]['OEVATTBE_TIMESTAMP'],
            ],
        ];

        $this->assertEquals($aExpectedData, $aData);

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
        $oVatGroupsGateway = oxNew(CategoryVATGroupsDbGateway::class);
        $oVatGroupsGateway->delete($sCategoryId);

        $this->assertSame(array(), $oVatGroupsGateway->load($sCategoryId));
    }

    /**
     * Test loading non existing VAT Group.
     */
    public function testLoadingEmptyVATGroup()
    {
        $oVatGroupsGateway = oxNew(CategoryVATGroupsDbGateway::class);
        $this->assertSame(array(), $oVatGroupsGateway->load('non_existing_group'));
    }

    /**
     * Test deleting non existing VAT Group.
     */
    public function testDeletingEmptyVATGroup()
    {
        $oVatGroupsGateway = oxNew(CategoryVATGroupsDbGateway::class);
        $this->assertNotSame(false, $oVatGroupsGateway->delete('non_existing_group'));
    }
}
