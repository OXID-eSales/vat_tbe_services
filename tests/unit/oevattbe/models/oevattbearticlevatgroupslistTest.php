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
 * Testing oeVATTBECountryVATGroup class.
 *
 * @covers oeVATTBEArticleVATGroupsList
 */
class Unit_oeVatTbe_Models_oeVATTBEArticleVATGroupsListTest extends OxidTestCase
{
    /**
     * Test saving of article groups list.
     */
    public function testSavingGroupsList()
    {
        $aExpectedData = array(
            'articleid' => 'articleId',
            'relations' => array(
                array(
                    'OEVATTBE_ARTICLEID' => 'articleId',
                    'OEVATTBE_COUNTRYID' => '8f241f110958b69e4.93886171',
                    'OEVATTBE_VATGROUPID' => '12',
                )
            )
        );
        /** @var oeVATTBEArticleVATGroupsDbGateway|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->getMock('oeVATTBEArticleVATGroupsDbGateway', array('save'));
        $oGateway->expects($this->once())->method('save')->with($aExpectedData);

        /** @var oeVATTBEArticleVATGroupsList $oList */
        $oList = oxNew('oeVATTBEArticleVATGroupsList', $oGateway);

        $oList->setId('articleId');

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
        /** @var oeVATTBEArticleVATGroupsDbGateway|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->getMock('oeVATTBEArticleVATGroupsDbGateway', array('save'));
        $oGateway->expects($this->once())->method('save')->with(array('articleid' => 'articleId', 'relations' => array()));

        /** @var oeVATTBEArticleVATGroupsList $oList */
        $oList = oxNew('oeVATTBEArticleVATGroupsList', $oGateway);

        $oList->setId('articleId');

        $aData = array(
            '8f241f110958b69e4.93886171' => '',
        );
        $oList->setData($aData);

        $oList->save();
    }

    /**
     * Test loading article groups list.
     */
    public function testLoadingEvidenceList()
    {
        $aData = array(
            array(
                'OEVATTBE_ARTICLEID' => 'articleId',
                'OEVATTBE_COUNTRYID' => '8f241f110958b69e4.93886171',
                'OEVATTBE_VATGROUPID' => '12',
                'OEVATTBE_TIMESTAMP' => '2014-05-05 19:00:00',
            ),
            array(
                'OEVATTBE_ARTICLEID' => 'articleId',
                'OEVATTBE_COUNTRYID' => 'a7c40f631fc920687.20179984',
                'OEVATTBE_VATGROUPID' => '11',
                'OEVATTBE_TIMESTAMP' => '2014-05-05 19:00:00',
            )
        );
        /** @var oeVATTBEArticleVATGroupsDbGateway|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->_createStub('oeVATTBEArticleVATGroupsDbGateway', array('load' => $aData));

        /** @var oeVATTBEArticleVATGroupsList $oList */
        $oList = oxNew('oeVATTBEArticleVATGroupsList', $oGateway);
        $oList->load('articleId');

        $aExpectedData = array(
            '8f241f110958b69e4.93886171' => '12',
            'a7c40f631fc920687.20179984' => '11',
        );
        $this->assertEquals($aExpectedData, $oList->getData());
    }

    /**
     * Test loading article groups list.
     */
    public function testLoadingEvidenceListById()
    {
        $aData = array(
            array(
                'OEVATTBE_ARTICLEID' => 'articleId1',
                'OEVATTBE_COUNTRYID' => '8f241f110958b69e4.93886171',
                'OEVATTBE_VATGROUPID' => '10',
                'OEVATTBE_TIMESTAMP' => '2014-05-05 19:00:00',
            ),
            array(
                'OEVATTBE_ARTICLEID' => 'articleId2',
                'OEVATTBE_COUNTRYID' => 'a7c40f631fc920687.20179984',
                'OEVATTBE_VATGROUPID' => '10',
                'OEVATTBE_TIMESTAMP' => '2014-05-05 19:00:00',
            )
        );
        /** @var oeVATTBEArticleVATGroupsDbGateway|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->_createStub('oeVATTBEArticleVATGroupsDbGateway', array('loadByGroupId' => $aData));

        /** @var oeVATTBEArticleVATGroupsList $oList */
        $oList = oxNew('oeVATTBEArticleVATGroupsList', $oGateway);

        $aExpectedData = array('articleId1', 'articleId2');
        $this->assertEquals($aExpectedData, $oList->getArticlesAssignedToGroup('10'));
    }

    /**
     * Test deleting article groups list.
     */
    public function testDeletingEvidenceList()
    {
        /** @var oeVATTBEArticleVATGroupsDbGateway|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->getMock('oeVATTBEArticleVATGroupsDbGateway', array('delete'));
        $oGateway->expects($this->once())->method('delete')->with('articleid');

        /** @var oeVATTBEArticleVATGroupsList $oList */
        $oList = oxNew('oeVATTBEArticleVATGroupsList', $oGateway);

        $oList->delete('articleid');
    }

    /**
     * Tests creating of oeVATTBEArticleVATGroupsList.
     */
    public function testCreatingListWithCreationMethod()
    {
        $oList = oeVATTBEArticleVATGroupsList::createArticleVATGroupsList();

        $this->assertInstanceOf('oeVATTBEArticleVATGroupsList', $oList);
    }
}
