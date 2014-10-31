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
 * Test class for.
 */
class Integration_oeVatTbe_VATGroups_oeVATTBECategoryVATGroupsPopulatorTest extends OxidTestCase
{
    /**
     * Tear down
     */
    public function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute('TRUNCATE TABLE `oevattbe_articlevat`');
        oxDb::getDb()->execute('TRUNCATE TABLE `oevattbe_categoryvat`');
        oxDb::getDb()->execute('TRUNCATE TABLE `oxobject2category`');
        oxDb::getDb()->execute('TRUNCATE TABLE `oxcategories`');
        oxDb::getDb()->execute('TRUNCATE TABLE `oxarticles`');
    }

    /**
     * test populate not existing category data
     *
     * @covers Article_Extend_Ajax
     */
    public function testPopulateAddingCategoriesToArticle()
    {
        $this->_prepareData();

        $this->setRequestParam('synchoxid', 'article1');

        $oController = $this->getMock('oeVATTBEArticle_Extend_Ajax', array('_getActionIds'));
        $oController->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('categoryId2')));

        $oController->addCat();

        $this->assertEquals(1, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(0, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(0, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     *
     * @covers Article_Extend_Ajax
     */
    public function testPopulateAddingCategoriesTBEToArticle()
    {
        $this->_prepareData();

        $this->setRequestParam('synchoxid', 'article1');

        $oController = $this->getMock('oeVATTBEArticle_Extend_Ajax', array('_getActionIds'));
        $oController->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('categoryId')));

        $oController->addCat();

        $this->assertEquals(1, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(2, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(1, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     *
     * @covers Category_Main_Ajax
     */
    public function testPopulateAddingArticleToCategory()
    {
        $this->_prepareData();

        $this->setRequestParam('synchoxid', 'categoryId2');

        $oController = $this->getMock('Category_Main_Ajax', array('_getActionIds'));
        $oController->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('article1')));

        $oController->addArticle();

        $this->assertEquals(1, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(0, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(0, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     *
     * @covers Category_Main_Ajax
     */
    public function testPopulateAddingArticleToCategoryTBE()
    {
        $this->_prepareData();

        $this->setRequestParam('synchoxid', 'categoryId');

        $oController = $this->getMock('oeVATTBECategory_Main_Ajax', array('_getActionIds'));
        $oController->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('article1')));
        $oController->addArticle();

        $this->assertEquals(1, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(2, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(1, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     *
     * @covers Article_Main
     */
    public function testPopulateAddingArticleToCategoryOnCreate()
    {
        $this->_prepareData();

        $oController = oxNew('oeVATTBEArticle_Main');
        $oController->addToCategory('categoryId2', 'article1');

        $this->assertEquals(1, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(0, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(0, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     *
     * @covers Article_Main
     */
    public function testPopulateAddingArticleToCategoryOnCreateTBECAtegory()
    {
        $this->_prepareData();

        $oController = oxNew('oeVATTBEArticle_Main');
        $oController->addToCategory('categoryId', 'article1');

        $this->assertEquals(1, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(2, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(1, $this->_getTBEServiceCount());
    }

    /**
     * prepare data for test case
     */
    protected function _prepareData()
    {
        $aSqlQueries = array();
        $aSqlQueries[] = "INSERT INTO `oevattbe_categoryvat` SET `OEVATTBE_CATEGORYID` = 'categoryId', `OEVATTBE_COUNTRYID` = 'a7c40f631fc920687.20179984', `OEVATTBE_VATGROUPID` = '10'";
        $aSqlQueries[] = "INSERT INTO `oevattbe_categoryvat` SET `OEVATTBE_CATEGORYID` = 'categoryId', `OEVATTBE_COUNTRYID` = 'a7c40f631fc920687.20179985', `OEVATTBE_VATGROUPID` = '11'";
        $aSqlQueries[] = "INSERT INTO `oxcategories` SET `oxid` = 'categoryId', `oevattbe_istbe` = '1'";
        $aSqlQueries[] = "INSERT INTO `oxcategories` SET `oxid` = 'categoryId2', `oevattbe_istbe` = '0'";
        $aSqlQueries[] = "INSERT INTO `oxarticles` SET `oxid` = 'article1', `oevattbe_istbeservice` = '0'";
        $aSqlQueries[] = "INSERT INTO `oxarticles` SET `oxid` = 'article2', `oevattbe_istbeservice` = '0'";

        foreach ($aSqlQueries as $sSql) {
            oxDb::getDb()->execute($sSql);
        }
    }

    /**
     * Returns assigned to category products count.
     *
     * @return int
     */
    protected function _getAssignedToCategoryProductsCount()
    {
        return oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oxobject2category`");
    }

    /**
     * Returns assigned vat groups to articles count
     *
     * @return int
     */
    protected function _getAssignedVATGroupsToArticles()
    {
        return oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oevattbe_articlevat`");
    }

    /**
     * Returns tbe service count
     *
     * @return int
     */
    protected function _getTBEServiceCount()
    {
        return oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oxarticles` WHERE  `oevattbe_istbeservice` = '1'");
    }
}
