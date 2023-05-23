<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EVatModule\Model\ArticleVATGroupsList;
use OxidEsales\EVatModule\Model\DbGateway\ArticleVATGroupsDbGateway;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Testing ArticleVATGroupsList class.
 */
class ArticleVATGroupsListTest extends TestCase
{
    use ContainerTrait;

    /**
     * Test saving of article groups list.
     */
    public function testSavingGroupsList()
    {
        $aExpectedData = [
            'articleid' => 'articleId',
            'relations' => [
                [
                    'OEVATTBE_ARTICLEID'  => 'articleId',
                    'OEVATTBE_COUNTRYID'  => '8f241f110958b69e4.93886171',
                    'OEVATTBE_VATGROUPID' => '12',
                ]
            ]
        ];
        /** @var ArticleVATGroupsDbGateway|MockObject $oGateway */
        $oGateway = $this->createPartialMock(ArticleVATGroupsDbGateway::class, ['save']);
        $oGateway->expects($this->once())->method('save')->with($aExpectedData);

        /** @var ArticleVATGroupsList $oList */
        $oList = oxNew(ArticleVATGroupsList::class, $oGateway);
        $oList->setId('articleId');
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
        /** @var ArticleVATGroupsDbGateway|MockObject $oGateway */
        $oGateway = $this->createPartialMock(ArticleVATGroupsDbGateway::class, ['save']);
        $oGateway->expects($this->once())->method('save')->with(['articleid' => 'articleId', 'relations' => []]);

        /** @var ArticleVATGroupsList $oList */
        $oList = oxNew(ArticleVATGroupsList::class, $oGateway);
        $oList->setId('articleId');
        $oList->setData([
            '8f241f110958b69e4.93886171' => '',
        ]);
        $oList->save();
    }

    /**
     * Test loading article groups list.
     */
    public function testLoadingArticleVATGroupsList()
    {
        $aData = [
            [
                'OEVATTBE_ARTICLEID'  => 'articleId',
                'OEVATTBE_COUNTRYID'  => '8f241f110958b69e4.93886171',
                'OEVATTBE_VATGROUPID' => '12',
                'OEVATTBE_TIMESTAMP'  => '2014-05-05 19:00:00',
            ],
            [
                'OEVATTBE_ARTICLEID'  => 'articleId',
                'OEVATTBE_COUNTRYID'  => 'a7c40f631fc920687.20179984',
                'OEVATTBE_VATGROUPID' => '11',
                'OEVATTBE_TIMESTAMP'  => '2014-05-05 19:00:00',
            ]
        ];
        /** @var ArticleVATGroupsDbGateway|MockObject $oGateway */
        $oGateway = $this->createStub(ArticleVATGroupsDbGateway::class);
        $oGateway->method('load')->willReturn($aData);

        /** @var ArticleVATGroupsList $oList */
        $oList = oxNew(ArticleVATGroupsList::class, $oGateway);
        $oList->load('articleId');

        $aExpectedData = [
            '8f241f110958b69e4.93886171' => '12',
            'a7c40f631fc920687.20179984' => '11',
        ];
        $this->assertEquals($aExpectedData, $oList->getData());
    }

    /**
     * Test loading article groups list.
     */
    public function testLoadingArticleVATGroupsListById()
    {
        $aData = [
            [
                'OEVATTBE_ARTICLEID'  => 'articleId1',
                'OEVATTBE_COUNTRYID'  => '8f241f110958b69e4.93886171',
                'OEVATTBE_VATGROUPID' => '10',
                'OEVATTBE_TIMESTAMP'  => '2014-05-05 19:00:00',
            ],
            [
                'OEVATTBE_ARTICLEID'  => 'articleId2',
                'OEVATTBE_COUNTRYID'  => 'a7c40f631fc920687.20179984',
                'OEVATTBE_VATGROUPID' => '10',
                'OEVATTBE_TIMESTAMP'  => '2014-05-05 19:00:00',
            ]
        ];
        /** @var ArticleVATGroupsDbGateway|MockObject $oGateway */
        $oGateway = $this->createStub(ArticleVATGroupsDbGateway::class);
        $oGateway->method('loadByGroupId')->willReturn($aData);

        /** @var ArticleVATGroupsList $oList */
        $oList = oxNew(ArticleVATGroupsList::class, $oGateway);

        $aExpectedData = ['articleId1', 'articleId2'];
        $this->assertEquals($aExpectedData, $oList->getArticlesAssignedToGroup('10'));
    }

    /**
     * Test deleting article groups list.
     */
    public function testDeletingArticleVATGroupsList()
    {
        /** @var ArticleVATGroupsDbGateway|MockObject $oGateway */
        $oGateway = $this->createPartialMock(ArticleVATGroupsDbGateway::class, ['delete']);
        $oGateway->expects($this->once())->method('delete')->with('articleid');

        /** @var ArticleVATGroupsList $oList */
        $oList = oxNew(ArticleVATGroupsList::class, $oGateway);
        $oList->delete('articleid');
    }

    /**
     * Tests creating of ArticleVATGroupsList.
     */
    public function testCreatingListWithCreationMethod()
    {
        $oList = $this->get(ArticleVATGroupsList::class);

        $this->assertInstanceOf(ArticleVATGroupsList::class, $oList);
    }
}
