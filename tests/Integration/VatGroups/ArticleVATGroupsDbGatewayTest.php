<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\VatGroups;

use OxidEsales\EVatModule\Model\DbGateway\ArticleVATGroupsDbGateway;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Test class for ArticleVATGroupsDbGateway.
 *
 * @covers ArticleVATGroupsDbGateway
 */
class ArticleVATGroupsDbGatewayTest extends BaseTestCase
{
    /**
     * Testing VAT Group saving to database.
     *
     * @return string Article id, for which record was saved.
     */
    public function testSavingVATGroupToDatabase()
    {
        /** @var ArticleVATGroupsDbGateway $oVatGroupsGateway */
        $oVatGroupsGateway = oxNew(ArticleVATGroupsDbGateway::class);
        $aData = array(
            'articleid' => '0962081a5693597654fd2887af7a6095',
            'relations' => array(
                array(
                    'OEVATTBE_ARTICLEID' => '0962081a5693597654fd2887af7a6095',
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
     * @param string $sArticleId Article id, for which group was created.
     *
     * @depends testSavingVATGroupToDatabase
     *
     * @return string Article id, for which record was saved.
     */
    public function testUpdatingVATGroupToDatabase($sArticleId)
    {
        /** @var ArticleVATGroupsDbGateway $oVatGroupsGateway */
        $oVatGroupsGateway = oxNew(ArticleVATGroupsDbGateway::class);
        $aData = array(
            'articleid' => '0962081a5693597654fd2887af7a6095',
            'relations' => array(
                array(
                    'OEVATTBE_ARTICLEID' => $sArticleId,
                    'OEVATTBE_COUNTRYID' => 'a7c40f631fc920687.20179984',
                    'OEVATTBE_VATGROUPID' => '11',
                ),
                array(
                    'OEVATTBE_ARTICLEID' => $sArticleId,
                    'OEVATTBE_COUNTRYID' => '8f241f110958b69e4.93886171',
                    'OEVATTBE_VATGROUPID' => '12',
                ),
            ),
        );
        $this->assertTrue($oVatGroupsGateway->save($aData));

        return $sArticleId;
    }

    /**
     * Testing VAT Group loading from database.
     *
     * @param string $sArticleId Article id, for which group was created.
     *
     * @depends testUpdatingVATGroupToDatabase
     *
     * @return string
     */
    public function testVATGroupLoading($sArticleId)
    {
        $oVatGroupsGateway = oxNew(ArticleVATGroupsDbGateway::class);
        $aData = $oVatGroupsGateway->load($sArticleId);

        $aExpectedData = array(
            array(
                'OEVATTBE_ARTICLEID' => $sArticleId,
                'OEVATTBE_COUNTRYID' => '8f241f110958b69e4.93886171',
                'OEVATTBE_VATGROUPID' => '12',
                'OEVATTBE_TIMESTAMP' => $aData[1]['OEVATTBE_TIMESTAMP'],
            ),
            array(
                'OEVATTBE_ARTICLEID' => $sArticleId,
                'OEVATTBE_COUNTRYID' => 'a7c40f631fc920687.20179984',
                'OEVATTBE_VATGROUPID' => '11',
                'OEVATTBE_TIMESTAMP' => $aData[0]['OEVATTBE_TIMESTAMP'],
            )
        );

        $this->assertSame($aExpectedData, $aData);

        return $sArticleId;
    }

    /**
     * Testing VAT Group loading from database by group id.
     *
     * @param string $sArticleId Article id, for which group was created.
     *
     * @depends testVATGroupLoading
     *
     * @return string
     */
    public function testVATGroupLoadingByGroupId($sArticleId)
    {
        /** @var ArticleVATGroupsDbGateway $oVatGroupsGateway */
        $oVatGroupsGateway = oxNew(ArticleVATGroupsDbGateway::class);
        $aData = $oVatGroupsGateway->loadByGroupId('12');

        $aExpectedData = array(
            array(
                'OEVATTBE_ARTICLEID' => $sArticleId,
                'OEVATTBE_COUNTRYID' => '8f241f110958b69e4.93886171',
                'OEVATTBE_VATGROUPID' => '12',
                'OEVATTBE_TIMESTAMP' => $aData[0]['OEVATTBE_TIMESTAMP'],
            ),
        );

        $this->assertSame($aExpectedData, $aData);

        return $sArticleId;
    }

    /**
     * Testing deletion of VAT Group from database.
     *
     * @param string $sArticleId article id
     *
     * @depends testVATGroupLoadingByGroupId
     */
    public function testDeletingVATGroupList($sArticleId)
    {
        $oVatGroupsGateway = oxNew(ArticleVATGroupsDbGateway::class);
        $oVatGroupsGateway->delete($sArticleId);

        $this->assertSame(array(), $oVatGroupsGateway->load($sArticleId));
    }

    /**
     * Test loading non existing VAT Group.
     */
    public function testLoadingEmptyVATGroup()
    {
        $oVatGroupsGateway = oxNew(ArticleVATGroupsDbGateway::class);
        $this->assertSame(array(), $oVatGroupsGateway->load('non_existing_group'));
    }

    /**
     * Test deleting non existing VAT Group.
     */
    public function testDeletingEmptyVATGroup()
    {
        $oVatGroupsGateway = oxNew(ArticleVATGroupsDbGateway::class);
        $this->assertNotSame(false, $oVatGroupsGateway->delete('non_existing_group'));
    }
}
