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
 * Test class for oeVATTBECategoryVATGroupsPopulatorDbGateway.
 *
 * @covers oeVATTBECategoryVATGroupsPopulatorDbGateway
 */
class Integration_oeVatTbe_VATGroups_oeVATTBECategoryVATGroupsPopulatorDbGatewayTest extends OxidTestCase
{
    /**
     * Test populate not existing category data.
     */
    public function testPopulateNotExistingCategory()
    {
        $this->_cleanData();
        $this->_prepareData();

        $oDbGateway = oxNew('oeVATTBECategoryVATGroupsPopulatorDbGateway');
        $oDbGateway->populate('categoryIdNotExist');

        $iRecordCount = oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oevattbe_articlevat`");
        $this->assertEquals(0, $iRecordCount);

        $iRecordCount = oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oxarticles` WHERE  `oevattbe_istbeservice` = '1'");
        $this->assertEquals(0, $iRecordCount);
    }

    /**
     * Test populate existing category data.
     *
     * @return oeVATTBECategoryVATGroupsPopulatorDbGateway
     */
    public function testPopulateExistingCategory()
    {
        $this->_cleanData();
        $this->_prepareData();

        $oDbGateway = oxNew('oeVATTBECategoryVATGroupsPopulatorDbGateway');
        $oDbGateway->populate('categoryId');

        $iRecordCount = oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oevattbe_articlevat`");
        $this->assertEquals(4, $iRecordCount);
        $iRecordCount = oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oxarticles` WHERE  `oevattbe_istbeservice` = '1'");
        $this->assertEquals(2, $iRecordCount);

        return $oDbGateway;
    }

    /**
     * Checks if reset works correctly for articles.
     *
     * @param oeVATTBECategoryVATGroupsPopulatorDbGateway $oDbGateway
     *
     * @depends testPopulateExistingCategory
     */
    public function testResetArticles($oDbGateway)
    {
        $aArticles = array(
            'article2'
        );

        $this->assertTrue($oDbGateway->reset($aArticles));
        $iRecordCount = oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oevattbe_articlevat`");
        $this->assertEquals(2, $iRecordCount);
        $iRecordCount = oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oxarticles` WHERE  `oevattbe_istbeservice` = '1'");
        $this->assertEquals(1, $iRecordCount);
    }

    /**
     * Tests when empty articles array is given.
     */
    public function testResetArticlesWhenGivenEmptyArray()
    {
        $aArticles = array();
        /** @var oeVATTBECategoryVATGroupsPopulatorDbGateway $oDbGateway */
        $oDbGateway = oxNew('oeVATTBECategoryVATGroupsPopulatorDbGateway');

        $this->assertFalse($oDbGateway->reset($aArticles));
    }

    /**
     * Truncates database tables.
     */
    private function _cleanData()
    {
        oxDb::getDb()->execute('TRUNCATE TABLE `oevattbe_articlevat`');
        oxDb::getDb()->execute('TRUNCATE TABLE `oevattbe_categoryvat`');
        oxDb::getDb()->execute('TRUNCATE TABLE `oxobject2category`');
        oxDb::getDb()->execute('TRUNCATE TABLE `oxcategories`');
        oxDb::getDb()->execute('TRUNCATE TABLE `oxarticles`');
    }

    /**
     * Prepare data for test case.
     */
    private function _prepareData()
    {
        $aSqlQueries = array();
        $aSqlQueries[] = "INSERT INTO `oevattbe_categoryvat` SET `OEVATTBE_CATEGORYID` = 'categoryId', `OEVATTBE_COUNTRYID` = 'a7c40f631fc920687.20179984', `OEVATTBE_VATGROUPID` = '10'";
        $aSqlQueries[] = "INSERT INTO `oevattbe_categoryvat` SET `OEVATTBE_CATEGORYID` = 'categoryId', `OEVATTBE_COUNTRYID` = 'a7c40f631fc920687.20179985', `OEVATTBE_VATGROUPID` = '11'";
        $aSqlQueries[] = "INSERT INTO `oxobject2category` SET `oxcatnid` = 'categoryId', `oxobjectid` = 'article1', `oxid` = 1";
        $aSqlQueries[] = "INSERT INTO `oxobject2category` SET `oxcatnid` = 'categoryId', `oxobjectid` = 'article2', `oxid` = 2";
        $aSqlQueries[] = "INSERT INTO `oxcategories` SET `oxid` = 'categoryId', `oevattbe_istbe` = '1'";
        $aSqlQueries[] = "INSERT INTO `oxarticles` SET `oxid` = 'article1', `oevattbe_istbeservice` = '0'";
        $aSqlQueries[] = "INSERT INTO `oxarticles` SET `oxid` = 'article2', `oevattbe_istbeservice` = '0'";

        foreach ($aSqlQueries as $sSql) {
            oxDb::getDb()->execute($sSql);
        }
    }
}
