<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\VisualCmsModule\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;

/**
 * Testing oeVATTBECategoryVATGroupsList class.
 *
 * @covers CategoryVATGroupsList
 */
class CategoryVATGroupsListTest extends TestCase
{
    /**
     * Test saving of category groups list.
     */
    public function testSavingGroupsList()
    {
        $aExpectedData = array(
            'categoryid' => 'categoryId',
            'relations' => array(
                array(
                    'OEVATTBE_CATEGORYID' => 'categoryId',
                    'OEVATTBE_COUNTRYID' => '8f241f110958b69e4.93886171',
                    'OEVATTBE_VATGROUPID' => '12',
                )
            )
        );
        /** @var oeVATTBECategoryVATGroupsDbGateway|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->getMock('oeVATTBECategoryVATGroupsDbGateway', array('save'));
        $oGateway->expects($this->once())->method('save')->with($aExpectedData);

        /** @var oeVATTBEArticleVATGroupsList $oList */
        $oList = oxNew('oeVATTBECategoryVATGroupsList', $oGateway);

        $oList->setId('categoryId');

        $aData = array(
            '8f241f110958b69e4.93886171' => '12',
        );
        $oList->setData($aData);

        $oList->save();
    }

    /**
     * Records with group id not set is passed;
     * These records should not be sent to gateway for recording.
     */
    public function testSavingGroupsListWhenRecordsWithNoGroupIsPassed()
    {
        /** @var oeVATTBECategoryVATGroupsDbGateway|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->getMock('oeVATTBECategoryVATGroupsDbGateway', array('save'));
        $oGateway->expects($this->once())->method('save')->with(array('categoryid' => 'categoryId', 'relations' => array()));

        /** @var oeVATTBECategoryVATGroupsList $oList */
        $oList = oxNew('oeVATTBECategoryVATGroupsList', $oGateway);

        $oList->setId('categoryId');

        $aData = array(
            '8f241f110958b69e4.93886171' => '',
        );
        $oList->setData($aData);

        $oList->save();
    }

    /**
     * Test loading category groups list.
     */
    public function testLoadingCategoryVATGroupsList()
    {
        $aData = array(
            array(
                'OEVATTBE_CATEGORYID' => 'categoryId',
                'OEVATTBE_COUNTRYID' => '8f241f110958b69e4.93886171',
                'OEVATTBE_VATGROUPID' => '12',
                'OEVATTBE_TIMESTAMP' => '2014-05-05 19:00:00',
            ),
            array(
                'OEVATTBE_CATEGORYID' => 'categoryId',
                'OEVATTBE_COUNTRYID' => 'a7c40f631fc920687.20179984',
                'OEVATTBE_VATGROUPID' => '11',
                'OEVATTBE_TIMESTAMP' => '2014-05-05 19:00:00',
            )
        );
        /** @var oeVATTBECategoryVATGroupsDbGateway|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->_createStub('oeVATTBECategoryVATGroupsDbGateway', array('load' => $aData));

        /** @var oeVATTBECategoryVATGroupsList $oList */
        $oList = oxNew('oeVATTBECategoryVATGroupsList', $oGateway);
        $oList->load('categoryId');

        $aExpectedData = array(
            '8f241f110958b69e4.93886171' => '12',
            'a7c40f631fc920687.20179984' => '11',
        );
        $this->assertEquals($aExpectedData, $oList->getData());
    }

    /**
     * Test deleting category groups list.
     */
    public function testDeletingCategoryVATGroupsList()
    {
        /** @var oeVATTBECategoryVATGroupsDbGateway|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->getMock('oeVATTBECategoryVATGroupsDbGateway', array('delete'));
        $oGateway->expects($this->once())->method('delete')->with('categoryid');

        /** @var oeVATTBECategoryVATGroupsList $oList */
        $oList = oxNew('oeVATTBECategoryVATGroupsList', $oGateway);

        $oList->delete('categoryid');
    }

    /**
     * Tests creating of oeVATTBECategoryVATGroupsList.
     */
    public function testCreating()
    {
        $oList = oeVATTBECategoryVATGroupsList::createInstance();

        $this->assertInstanceOf('oeVATTBECategoryVATGroupsList', $oList);
    }
}
