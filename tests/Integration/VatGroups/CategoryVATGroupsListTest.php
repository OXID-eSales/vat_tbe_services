<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\VatGroups;

use PHPUnit\Framework\TestCase;

/**
 * Testing CategoryVATGroupsList class.
 *
 * @covers CategoryVATGroupsList
 * @covers CategoryVATGroupsDbGateway
 */
class CategoryVATGroupsListTest extends TestCase
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

        $oGroupsList = oeVATTBECategoryVATGroupsList::createInstance();
        $oGroupsList->setId('categoryId');
        $oGroupsList->setData($aData);
        $this->assertEquals('categoryId', $oGroupsList->save());

        return 'categoryId';
    }

    /**
     * Two Country Groups exits;
     * List is successfully loaded and array of groups is returned.
     *
     * @param string $sCategoryId category id
     *
     * @depends testSavingGroupsList
     */
    public function testLoadingGroupsListWhenGroupsExists($sCategoryId)
    {
        $oGroupsList = oeVATTBECategoryVATGroupsList::createInstance();

        $aExpectedData = array(
            'germanyid' => '12',
            'lithuaniaid' => '13'
        );
        $oGroupsList->load($sCategoryId);

        $this->assertEquals($aExpectedData, $oGroupsList->getData());
    }

    /**
     * No Country Groups exits;
     * List is successfully loaded and empty array is returned.
     */
    public function testLoadingGroupsListWhenNoGroupsExists()
    {
        $oGroupsList = oeVATTBECategoryVATGroupsList::createInstance();
        $oGroupsList->load('NonExistingCountryId');

        $this->assertEquals(array(), $oGroupsList->getData());
    }

}
