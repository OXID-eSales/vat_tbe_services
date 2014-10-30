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
 * Testing oeVATTBEArticleVATGroupsList class.
 *
 * @covers oeVATTBEArticleVATGroupsList
 * @covers oeVATTBEArticleVATGroupsDbGateway
 */
class Integration_oeVatTbe_VATGroups_oeVATTBEArticleVATGroupsListTest extends OxidTestCase
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
