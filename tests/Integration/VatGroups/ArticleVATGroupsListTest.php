<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\VatGroups;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EVatModule\Model\ArticleVATGroupsList;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Testing oeVATTBEArticleVATGroupsList class.
 */
class ArticleVATGroupsListTest extends BaseTestCase
{
    use ContainerTrait;

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

        $oGroupsList = $this->get(ArticleVATGroupsList::class);
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
        $oGroupsList = $this->get(ArticleVATGroupsList::class);

        $aExpectedData = array(
            'GermanyId' => '12',
            'LithuaniaId' => '13'
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
        $oGroupsList = $this->get(ArticleVATGroupsList::class);
        $oGroupsList->load('NonExistingCountryId');

        $this->assertEquals(array(), $oGroupsList->getData());
    }
}
