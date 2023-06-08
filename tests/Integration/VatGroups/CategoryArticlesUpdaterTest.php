<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\VatGroups;

use OxidEsales\EVatModule\Controller\Admin\ArticleExtendAjax;
use OxidEsales\EVatModule\Controller\Admin\ArticleMain;
use OxidEsales\EVatModule\Controller\Admin\CategoryAdministration;
use OxidEsales\EVatModule\Controller\Admin\CategoryMainAjax;
use OxidEsales\EVatModule\Shop\Category;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Test class for.
 */
class CategoryArticlesUpdaterTest extends BaseTestCase
{
    /**
     * test populate not existing category data
     */
    public function testPopulateAddingCategoriesToArticle()
    {
        $this->_cleanData();
        $this->_prepareData();

        $_POST['synchoxid'] = 'article1';

        $oController = $this->getMockBuilder(ArticleExtendAjax::class)
                ->onlyMethods(array("getActionIds"))
                ->getMock();
        $oController->expects($this->any())->method('getActionIds')->will($this->returnValue(array('categoryId2')));

        $oController->addCat();

        $this->assertEquals(1, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(0, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(0, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     */
    public function testPopulateAddingCategoriesTBEToArticle()
    {
        $this->_cleanData();
        $this->_prepareData();

        $_POST['synchoxid'] = 'article1';

        /** @var ArticleExtendAjax $oController */
        $oController = $this->getMockBuilder(ArticleExtendAjax::class)
                ->onlyMethods(array("getActionIds"))
                ->getMock();
        $oController->expects($this->any())->method('getActionIds')->will($this->returnValue(array('categoryId')));

        $oController->addCat();

        $this->assertEquals(1, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(2, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(1, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     */
    public function testPopulateAddingArticleToCategory()
    {
        $this->_cleanData();
        $this->_prepareData();

        $_POST['synchoxid'] = 'categoryId2';

        $oController = $this->getMockBuilder(CategoryMainAjax::class)
                ->onlyMethods(array("getActionIds"))
                ->getMock();
        $oController->expects($this->any())->method('getActionIds')->will($this->returnValue(array('article1')));

        $oController->addArticle();

        $this->assertEquals(1, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(0, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(0, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     */
    public function testPopulateAddingArticleToCategoryTBE()
    {
        $this->_cleanData();
        $this->_prepareData();

        $_POST['synchoxid'] = 'categoryId';

        $oController = $this->getMockBuilder(CategoryMainAjax::class)
                ->onlyMethods(array("getActionIds"))
                ->getMock();
        $oController->expects($this->any())->method('getActionIds')->will($this->returnValue(array('article1')));
        $oController->addArticle();

        $this->assertEquals(1, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(2, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(1, $this->_getTBEServiceCount());
    }

    /**
     * Test check when 1 article is unassigned from category.
     *
     * @depends testPopulateAddingArticleToCategoryTBE
     */
    public function testRemoveArticleFromCategoryWhenOneArticleIsRemoved()
    {
        $this->_cleanFixtures();

        /** @var CategoryMainAjax $oController */
        $oController = $this->getMockBuilder(CategoryMainAjax::class)
                ->onlyMethods(array("getActionIds"))
                ->getMock();
        $oController->expects($this->any())->method('getActionIds')->will($this->returnValue(array('article1')));
        $oController->removeArticle();

        $this->assertEquals(0, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(0, $this->_getTBEServiceCount());
    }

    /**
     * When all articles are unassigned by clicking button "Unassign all".
     */
    public function testRemoveArticlesWhenUnsassignAllIsClicked()
    {
        $this->_cleanData();
        $this->_prepareDataForRemovingArticlesFromCategory();

        $_POST['synchoxid'] = 'categoryId';

        /** @var CategoryMainAjax $oController */
        $oController = $this->getMockBuilder(CategoryMainAjax::class)
                ->onlyMethods(array('getActionIds', 'getAll', 'addFilter'))
                ->getMock();
        $oController->expects($this->atLeastOnce())->method('getAll')->will($this->returnValue(array('article3', 'article4')));

        $_POST['all'] = 1;
        $oController->removeArticle();

        $this->assertEquals(1, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(1, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     */
    public function testPopulateAddingArticleToCategoryOnCreate()
    {
        $this->_cleanData();
        $this->_prepareData();

        $oController = oxNew(ArticleMain::class);
        $oController->addToCategory('categoryId2', 'article1');

        $this->assertEquals(1, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(0, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(0, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     */
    public function testPopulateAddingArticleToCategoryOnCreateTBECAtegory()
    {
        $this->_cleanData();
        $this->_prepareData();

        $oController = oxNew(ArticleMain::class);
        $oController->addToCategory('categoryId', 'article1');

        $this->assertEquals(1, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(2, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(1, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     */
    public function testPopulateOnCategoryConfiguration()
    {
        $this->_cleanData();
        $this->_prepareData(true);

        $_POST['oxid'] = 'categoryId';
        $_POST['editval'] = array('oevattbe_istbe' => 1);
        $aSelectParams = array(
            'a7c40f631fc920687.20179984' => 10,
            'a7c40f631fc920687.20179985' => 11
        );
        $_POST['VATGroupsByCountry'] = $aSelectParams;

        $oController = oxNew(CategoryAdministration::class);
        $oController->save();

        $this->assertEquals(2, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(4, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(2, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     */
    public function testPopulateOnCategoryConfigurationNotTbe()
    {
        $this->_cleanData();
        $this->_prepareData(true);

        $_POST['oxid'] = 'categoryId';
        $_POST['editval'] = array('oevattbe_istbe' => 0);
        $aSelectParams = array(
            'a7c40f631fc920687.20179984' => 10,
            'a7c40f631fc920687.20179985' => 11
        );
        $_POST['VATGroupsByCountry'] = $aSelectParams;

        $oController = oxNew(CategoryAdministration::class);
        $oController->save();

        $this->assertEquals(2, $this->_getAssignedToCategoryProductsCount());
        $this->assertEquals(4, $this->_getAssignedVATGroupsToArticles());
        $this->assertEquals(0, $this->_getTBEServiceCount());
    }

    /**
     * test populate not existing category data
     */
    public function testPopulateOnCategoryConfigurationNoGroups()
    {
        $this->_cleanData();
        $this->_prepareData(true);

        $_POST['oxid'] = 'categoryId';
        $_POST['editval'] = array('oevattbe_istbe' => 1);
        $aSelectParams = array();
        $_POST['VATGroupsByCountry'] = $aSelectParams;

        $oController = oxNew(CategoryAdministration::class);
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
            \oxDb::getDb()->execute($sSql);
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
            \oxDb::getDb()->execute($sSql);
        }
    }

    protected function _cleanFixtures()
    {
        \oxDb::getDb()->execute("DELETE FROM `oevattbe_articlevat` WHERE OEVATTBE_ARTICLEID IN('1126', '1127', '1128', '1131')");
        \oxDb::getDb()->execute("DELETE FROM `oxarticles` WHERE OXID IN ('1126', '1127', '1131', '_testArticle')");
    }

    /**
     * Truncates tables.
     */
    protected function _cleanData()
    {
        \oxDb::getDb()->execute('TRUNCATE TABLE `oevattbe_articlevat`');
        \oxDb::getDb()->execute('TRUNCATE TABLE `oevattbe_categoryvat`');
        \oxDb::getDb()->execute('TRUNCATE TABLE `oxobject2category`');
        \oxDb::getDb()->execute('TRUNCATE TABLE `oxcategories`');
        \oxDb::getDb()->execute('TRUNCATE TABLE `oxarticles`');
    }

    /**
     * Returns assigned to category products count.
     *
     * @return int
     */
    protected function _getAssignedToCategoryProductsCount()
    {
        return \oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oxobject2category`");
    }

    /**
     * Returns assigned vat groups to articles count
     *
     * @return int
     */
    protected function _getAssignedVATGroupsToArticles()
    {
        return \oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oevattbe_articlevat`");
    }

    /**
     * Returns tbe service count
     *
     * @return int
     */
    protected function _getTBEServiceCount()
    {
        return \oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oxarticles` WHERE  `oevattbe_istbeservice` = '1'");
    }

    /**
     * Helper to create category
     *
     * @param string $sCategoryId category id which be used to create new category.
     * @param bool   $bIsTbe      bool if new category is TBE.
     *
     * @return Category
     */
    private function _createCategory($sCategoryId, $bIsTbe)
    {
        /** @var Category $oCategory */
        $oCategory = oxNew(Category::class);
        $oCategory->setId($sCategoryId);
        $oCategory->assign([
            'oevattbe_istbe' => $bIsTbe,
            'oxparentid'     => 'oxrootid',
        ]);
        $oCategory->save();
        return $oCategory;
    }
}
