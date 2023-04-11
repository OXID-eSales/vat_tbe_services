<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\VatGroups;

use PHPUnit\Framework\TestCase;

/**
 * Testing oeVATTBEArticleVATGroupsList class.
 *
 * @covers ArticleVATGroupsList
 * @covers ArticleVATGroupsDbGateway
 */
class ArticleVATGroupsListTest extends TestCase
{
    /**
     * Relations for two countries is passed;
     * Both relations should be added to database for set article.
     *
     * @return string
     */
    public function testSavingGroupsList()
    {
        $aData = array(
            'GermanyId' => '12',
            'LithuaniaId' => '13'
        );

        $oGroupsList = oeVATTBEArticleVATGroupsList::createInstance();
        $oGroupsList->setId('articleId');
        $oGroupsList->setData($aData);
        $this->assertEquals('articleId', $oGroupsList->save());

        return 'articleId';
    }

    /**
     * Two Country Groups exits;
     * List is successfully loaded and array of groups is returned.
     *
     * @param string $sArticleId article id
     *
     * @depends testSavingGroupsList
     */
    public function testLoadingGroupsListWhenGroupsExists($sArticleId)
    {
        $oGroupsList = oeVATTBEArticleVATGroupsList::createInstance();

        $aExpectedData = array(
            'germanyid' => '12',
            'lithuaniaid' => '13'
        );
        $oGroupsList->load($sArticleId);

        $this->assertEquals($aExpectedData, $oGroupsList->getData());
    }

    /**
     * No Country Groups exits;
     * List is successfully loaded and empty array is returned.
     */
    public function testLoadingGroupsListWhenNoGroupsExists()
    {
        $oGroupsList = oeVATTBEArticleVATGroupsList::createInstance();
        $oGroupsList->load('NonExistingCountryId');

        $this->assertEquals(array(), $oGroupsList->getData());
    }
}
