<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EVatModule\Model\CategoryVATGroupsList;
use OxidEsales\EVatModule\Model\DbGateway\CategoryVATGroupsDbGateway;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Testing CategoryVATGroupsList class.
 */
class CategoryVATGroupsListTest extends TestCase
{
    use ContainerTrait;

    /**
     * Test saving of category groups list.
     */
    public function testSavingGroupsList()
    {
        $aExpectedData = [
            'categoryid' => 'categoryId',
            'relations'  => [
                [
                    'OEVATTBE_CATEGORYID' => 'categoryId',
                    'OEVATTBE_COUNTRYID'  => '8f241f110958b69e4.93886171',
                    'OEVATTBE_VATGROUPID' => '12',
                ]
            ]
        ];
        /** @var CategoryVATGroupsDbGateway|MockObject $oGateway */
        $oGateway = $this->createPartialMock(CategoryVATGroupsDbGateway::class, ['save']);
        $oGateway->expects($this->once())->method('save')->with($aExpectedData);

        /** @var CategoryVATGroupsList $oList */
        $oList = oxNew(CategoryVATGroupsList::class, $oGateway);
        $oList->setId('categoryId');
        $oList->setData([
            '8f241f110958b69e4.93886171' => '12',
        ]);
        $oList->save();
    }

    /**
     * Records with group id not set is passed;
     * These records should not be sent to gateway for recording.
     */
    public function testSavingGroupsListWhenRecordsWithNoGroupIsPassed()
    {
        /** @var CategoryVATGroupsDbGateway|MockObject $oGateway */
        $oGateway = $this->createPartialMock(CategoryVATGroupsDbGateway::class, ['save']);
        $oGateway->expects($this->once())->method('save')->with(['categoryid' => 'categoryId', 'relations' => []]);

        /** @var CategoryVATGroupsList $oList */
        $oList = oxNew(CategoryVATGroupsList::class, $oGateway);
        $oList->setId('categoryId');
        $oList->setData([
            '8f241f110958b69e4.93886171' => '',
        ]);
        $oList->save();
    }

    /**
     * Test loading category groups list.
     */
    public function testLoadingCategoryVATGroupsList()
    {
        $aData = [
            [
                'OEVATTBE_CATEGORYID' => 'categoryId',
                'OEVATTBE_COUNTRYID'  => '8f241f110958b69e4.93886171',
                'OEVATTBE_VATGROUPID' => '12',
                'OEVATTBE_TIMESTAMP'  => '2014-05-05 19:00:00',
            ],
            [
                'OEVATTBE_CATEGORYID' => 'categoryId',
                'OEVATTBE_COUNTRYID'  => 'a7c40f631fc920687.20179984',
                'OEVATTBE_VATGROUPID' => '11',
                'OEVATTBE_TIMESTAMP'  => '2014-05-05 19:00:00',
            ]
        ];
        /** @var CategoryVATGroupsDbGateway|MockObject $oGateway */
        $oGateway = $this->createStub(CategoryVATGroupsDbGateway::class);
        $oGateway->method('load')->willReturn($aData);

        /** @var CategoryVATGroupsList $oList */
        $oList = oxNew(CategoryVATGroupsList::class, $oGateway);
        $oList->load('categoryId');

        $aExpectedData = [
            '8f241f110958b69e4.93886171' => '12',
            'a7c40f631fc920687.20179984' => '11',
        ];
        $this->assertEquals($aExpectedData, $oList->getData());
    }

    /**
     * Test deleting category groups list.
     */
    public function testDeletingCategoryVATGroupsList()
    {
        /** @var CategoryVATGroupsDbGateway|MockObject $oGateway */
        $oGateway = $this->createPartialMock(CategoryVATGroupsDbGateway::class, ['delete']);
        $oGateway->expects($this->once())->method('delete')->with('categoryid');

        /** @var CategoryVATGroupsList $oList */
        $oList = oxNew(CategoryVATGroupsList::class, $oGateway);

        $oList->delete('categoryid');
    }

    /**
     * Tests creating of CategoryVATGroupsList.
     */
    public function testCreating()
    {
        $oList = $this->get(CategoryVATGroupsList::class);

        $this->assertInstanceOf(CategoryVATGroupsList::class, $oList);
    }
}
