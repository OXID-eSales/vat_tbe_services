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
 * Testing oeVATTBECategoryVATGroupsList class.
 *
 * @covers oeVATTBECategoryVATGroupsList
 */
class Unit_oeVatTbe_Models_oeVATTBECategoryVATGroupsListTest extends OxidTestCase
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
