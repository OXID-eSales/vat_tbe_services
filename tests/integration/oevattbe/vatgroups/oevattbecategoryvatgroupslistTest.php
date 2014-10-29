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
 * @covers oeVATTBECategoryVATGroupsDbGateway
 */
class Integration_oeVatTbe_VATGroups_oeVATTBECategoryVATGroupsListTest extends OxidTestCase
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
