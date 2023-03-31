<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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

        $this->setRequestParameter('synchoxid', 'article1');

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

        $this->setRequestParameter('synchoxid', 'article1');

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

        $this->setRequestParameter('synchoxid', 'categoryId2');

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

        $this->setRequestParameter('synchoxid', 'categoryId');

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

        $this->setRequestParameter('synchoxid', 'categoryId');

        /** @var oeVATTBECategory_Main_Ajax|PHPUnit_Framework_MockObject_MockObject $oController */
        $oController = $this->getMock('oeVATTBECategory_Main_Ajax', array('_getActionIds', '_getAll', '_addFilter'));
        $oController->expects($this->atLeastOnce())->method('_getAll')->will($this->returnValue(array('article3', 'article4')));

        $this->setRequestParameter('all', 1);
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

        $this->setRequestParameter('oxid', 'categoryId');
        $this->setRequestParameter('editval', array('oevattbe_istbe' => 1));
        $aSelectParams = array(
            'a7c40f631fc920687.20179984' => 10,
            'a7c40f631fc920687.20179985' => 11
        );
        $this->setRequestParameter('VATGroupsByCountry', $aSelectParams);

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

        $this->setRequestParameter('oxid', 'categoryId');
        $this->setRequestParameter('editval', array('oevattbe_istbe' => 0));
        $aSelectParams = array(
            'a7c40f631fc920687.20179984' => 10,
            'a7c40f631fc920687.20179985' => 11
        );
        $this->setRequestParameter('VATGroupsByCountry', $aSelectParams);

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

        $this->setRequestParameter('oxid', 'categoryId');
        $this->setRequestParameter('editval', array('oevattbe_istbe' => 1));
        $aSelectParams = array();
        $this->setRequestParameter('VATGroupsByCountry', $aSelectParams);

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

        $this->_createCategory('categoryId', true);
        $this->_createCategory('categoryId2', false);
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

    /**
     * Helper to create category
     *
     * @param string $sCategoryId category id which be used to create new category.
     * @param bool   $bIsTbe      bool if new category is TBE.
     *
     * @return oxCategory
     */
    private function _createCategory($sCategoryId, $bIsTbe)
    {
        /** @var oxCategory $oCategory */
        $oCategory = oxNew('oxCategory');
        $oCategory->setId($sCategoryId);
        $oCategory->oxcategories__oevattbe_istbe = new oxField($bIsTbe);
        $oCategory->oxcategories__oxparentid = new oxField('oxrootid');
        $oCategory->save();
        return $oCategory;
    }
}
