<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;

/**
 * Testing oeVATTBEArticleVATGroupsList class.
 *
 * @covers ArticleVATGroupsList
 */
class ArticleVATGroupsListTest extends TestCase
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
    public function testLoadingArticleVATGroupsList()
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
    public function testLoadingArticleVATGroupsListById()
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
    public function testDeletingArticleVATGroupsList()
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
        $oList = oeVATTBEArticleVATGroupsList::createInstance();

        $this->assertInstanceOf('oeVATTBEArticleVATGroupsList', $oList);
    }
}
