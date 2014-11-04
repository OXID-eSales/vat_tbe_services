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
class Integration_oeVatTbe_VATGroups_oeVATTBECategoryArticlesUpdaterTest extends OxidTestCase
{
    /**
     * test populate not existing category data
     *
     * @covers oeVATTBEArticle_Extend_Ajax
     */
    public function testPopulateAddingCategoriesToArticle()
    {
        $this->_cleanData();
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
     * @covers oeVATTBEArticle_Extend_Ajax
     */
    public function testPopulateAddingCategoriesTBEToArticle()
    {
        $this->_cleanData();
        $this->_prepareData();

        $this->setRequestParam('synchoxid', 'article1');

        /** @var oeVATTBEArticle_Extend_Ajax|PHPUnit_Framework_MockObject_MockObject $oController */
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
     * @covers oeVATTBECategory_Main_Ajax
     */
    public function testPopulateAddingArticleToCategory()
    {
        $this->_cleanData();
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
     * @covers oeVATTBECategory_Main_Ajax
     */
    public function testPopulateAddingArticleToCategoryTBE()
    {
        $this->_cleanData();
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
     * Test check when 1 article is unassigned from category.
     *
     * @depends testPopulateAddingArticleToCategoryTBE
     *
     * @covers oeVATTBECategory_Main_Ajax
     */
    public function testRemoveArticleFromCategoryWhenOneArticleIsRemoved()
    {
        /** @var oeVATTBECategory_Main_Ajax|PHPUnit_Framework_MockObject_MockObject $oController */
        $oController = $this->getMock('oeVATTBECategory_Main_Ajax', array('_getActionIds'));
        $oController->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('article1')));
        $oController->removeArticle();

        $this->assertEquals(0, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(0, $this->_getTBEServiceCount());
    }

    /**
     * When all articles are unassigned by clicking button "Unassign all".
     *
     * @covers oeVATTBECategory_Main_Ajax
     */
    public function testRemoveArticlesWhenUnsassignAllIsClicked()
    {
        $this->_cleanData();
        $this->_prepareDataForRemovingArticlesFromCategory();

        $this->setRequestParam('synchoxid', 'categoryId');

        /** @var oeVATTBECategory_Main_Ajax|PHPUnit_Framework_MockObject_MockObject $oController */
        $oController = $this->getMock('oeVATTBECategory_Main_Ajax', array('_getActionIds', '_getAll', '_addFilter'));
        $oController->expects($this->atLeastOnce())->method('_getAll')->will($this->returnValue(array('article3', 'article4')));

        $this->setRequestParam('all', 1);
        $oController->removeArticle();

        $this->assertEquals(1, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(1, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     *
     * @covers oeVATTBEArticle_Main
     */
    public function testPopulateAddingArticleToCategoryOnCreate()
    {
        $this->_cleanData();
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
     * @covers oeVATTBEArticle_Main
     */
    public function testPopulateAddingArticleToCategoryOnCreateTBECAtegory()
    {
        $this->_cleanData();
        $this->_prepareData();

        $oController = oxNew('oeVATTBEArticle_Main');
        $oController->addToCategory('categoryId', 'article1');

        $this->assertEquals(1, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(2, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(1, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     *
     * @covers oeVATTBECategoryAdministration
     */
    public function testPopulateOnCategoryConfiguration()
    {
        $this->_cleanData();
        $this->_prepareData(true);

        $this->setRequestParam('oxid', 'categoryId');
        $this->setRequestParam('editval', array('oevattbe_istbe' => 1));
        $aSelectParams = array(
            'a7c40f631fc920687.20179984' => 10,
            'a7c40f631fc920687.20179985' => 11
        );
        $this->setRequestParam('VATGroupsByCountry', $aSelectParams);

        $oController = oxNew('oeVATTBECategoryAdministration');
        $oController->save();

        $this->assertEquals(2, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(4, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(2, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     *
     * @covers oeVATTBECategoryAdministration
     */
    public function testPopulateOnCategoryConfigurationNotTbe()
    {
        $this->_cleanData();
        $this->_prepareData(true);

        $this->setRequestParam('oxid', 'categoryId');
        $this->setRequestParam('editval', array('oevattbe_istbe' => 0));
        $aSelectParams = array(
            'a7c40f631fc920687.20179984' => 10,
            'a7c40f631fc920687.20179985' => 11
        );
        $this->setRequestParam('VATGroupsByCountry', $aSelectParams);

        $oController = oxNew('oeVATTBECategoryAdministration');
        $oController->save();

        $this->assertEquals(2, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(4, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(0, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     *
     * @covers oeVATTBECategoryAdministration
     */
    public function testPopulateOnCategoryConfigurationNoGroups()
    {
        $this->_cleanData();
        $this->_prepareData(true);

        $this->setRequestParam('oxid', 'categoryId');
        $this->setRequestParam('editval', array('oevattbe_istbe' => 1));
        $aSelectParams = array();
        $this->setRequestParam('VATGroupsByCountry', $aSelectParams);

        $oController = oxNew('oeVATTBECategoryAdministration');
        $oController->save();

        $this->assertEquals(2, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(0, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(2, $this->_getTBEServiceCount());
    }

    /**
     * Prepare data for test case.
     *
     * @param bool $blAssign assign products to category or not
     */
    protected function _prepareData($blAssign = false)
    {
        $aSqlQueries = array();
        $aSqlQueries[] = "INSERT INTO `oevattbe_categoryvat` SET `OEVATTBE_CATEGORYID` = 'categoryId', `OEVATTBE_COUNTRYID` = 'a7c40f631fc920687.20179984', `OEVATTBE_VATGROUPID` = '10'";
        $aSqlQueries[] = "INSERT INTO `oevattbe_categoryvat` SET `OEVATTBE_CATEGORYID` = 'categoryId', `OEVATTBE_COUNTRYID` = 'a7c40f631fc920687.20179985', `OEVATTBE_VATGROUPID` = '11'";
        if ($blAssign) {
            $aSqlQueries[] = "INSERT INTO `oxobject2category` SET `oxcatnid` = 'categoryId', `oxobjectid` = 'article1', `oxid` = 1";
            $aSqlQueries[] = "INSERT INTO `oxobject2category` SET `oxcatnid` = 'categoryId', `oxobjectid` = 'article2', `oxid` = 2";
        }
        $aSqlQueries[] = "INSERT INTO `oxcategories` SET `oxid` = 'categoryId', `oevattbe_istbe` = '1'";
        $aSqlQueries[] = "INSERT INTO `oxcategories` SET `oxid` = 'categoryId2', `oevattbe_istbe` = '0'";
        $aSqlQueries[] = "INSERT INTO `oxarticles` SET `oxid` = 'article1', `oevattbe_istbeservice` = '0'";
        $aSqlQueries[] = "INSERT INTO `oxarticles` SET `oxid` = 'article2', `oevattbe_istbeservice` = '0'";

        foreach ($aSqlQueries as $sSql) {
            oxDb::getDb()->execute($sSql);
        }
    }

    /**
     * Adds some records to DB for testing.
     */
    protected function _prepareDataForRemovingArticlesFromCategory()
    {
        $aSqlQueries[] = "INSERT INTO `oxarticles` SET `oxid` = 'article3', `oevattbe_istbeservice` = '1'";
        $aSqlQueries[] = "INSERT INTO `oxarticles` SET `oxid` = 'article4', `oevattbe_istbeservice` = '1'";
        $aSqlQueries[] = "INSERT INTO `oxarticles` SET `oxid` = 'article5', `oevattbe_istbeservice` = '1'";
        $aSqlQueries[] = "INSERT INTO `oevattbe_articlevat` SET `OEVATTBE_ARTICLEID` = 'article3', `OEVATTBE_COUNTRYID` = 'a7c40f631fc920687.20179984', `OEVATTBE_VATGROUPID` = 10";
        $aSqlQueries[] = "INSERT INTO `oevattbe_articlevat` SET `OEVATTBE_ARTICLEID` = 'article4', `OEVATTBE_COUNTRYID` = 'a7c40f631fc920687.20179984', `OEVATTBE_VATGROUPID` = 10";
        $aSqlQueries[] = "INSERT INTO `oevattbe_articlevat` SET `OEVATTBE_ARTICLEID` = 'article5', `OEVATTBE_COUNTRYID` = 'a7c40f631fc920687.20179984', `OEVATTBE_VATGROUPID` = 10";

        foreach ($aSqlQueries as $sSql) {
            oxDb::getDb()->execute($sSql);
        }
    }

    /**
     * Truncates tables.
     */
    protected function _cleanData()
    {
        oxDb::getDb()->execute('TRUNCATE TABLE `oevattbe_articlevat`');
        oxDb::getDb()->execute('TRUNCATE TABLE `oevattbe_categoryvat`');
        oxDb::getDb()->execute('TRUNCATE TABLE `oxobject2category`');
        oxDb::getDb()->execute('TRUNCATE TABLE `oxcategories`');
        oxDb::getDb()->execute('TRUNCATE TABLE `oxarticles`');
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
