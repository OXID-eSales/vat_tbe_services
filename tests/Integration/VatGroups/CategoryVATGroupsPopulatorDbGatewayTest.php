<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\VatGroups;

use OxidEsales\EVatModule\Model\DbGateway\CategoryVATGroupsPopulatorDbGateway;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Test class for CategoryVATGroupsPopulatorDbGateway.
 */
class CategoryVATGroupsPopulatorDbGatewayTest extends BaseTestCase
{
    /**
     * Test populate not existing category data.
     */
    public function testPopulateNotExistingCategory()
    {
        $this->_cleanData();
        $this->_prepareData();

        $oDbGateway = oxNew(CategoryVATGroupsPopulatorDbGateway::class);
        $oDbGateway->populate('categoryIdNotExist');

        $iRecordCount = \oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oevattbe_articlevat`");
        $this->assertEquals(0, $iRecordCount);

        $iRecordCount = \oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oxarticles` WHERE  `oevattbe_istbeservice` = '1'");
        $this->assertEquals(0, $iRecordCount);
    }

    /**
     * Test populate existing category data.
     *
     * @return CategoryVATGroupsPopulatorDbGateway
     */
    public function testPopulateExistingCategory()
    {
        $this->_cleanData();
        $this->_prepareData();

        //TODO: remove this and use only fixtures
        $this->_cleanFixtures();

        $oDbGateway = oxNew(CategoryVATGroupsPopulatorDbGateway::class);
        $oDbGateway->populate('categoryId');

        $iRecordCount = \oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oevattbe_articlevat`");
        $this->assertEquals(4, $iRecordCount);
        $iRecordCount = \oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oxarticles` WHERE  `oevattbe_istbeservice` = '1'");
        $this->assertEquals(2, $iRecordCount);

        return $oDbGateway;
    }

    /**
     * Checks if reset works correctly for articles.
     *
     * @param CategoryVATGroupsPopulatorDbGateway $oDbGateway
     *
     * @depends testPopulateExistingCategory
     */
    public function testResetArticles($oDbGateway)
    {
        //TODO: remove this and use only fixtures
        $this->_cleanFixtures();

        $aArticles = array(
            'article2'
        );

        $this->assertTrue($oDbGateway->reset($aArticles));
        $iRecordCount = \oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oevattbe_articlevat`");
        $this->assertEquals(2, $iRecordCount);
        $iRecordCount = \oxDb::getDb()->getOne("SELECT COUNT(*) FROM `oxarticles` WHERE  `oevattbe_istbeservice` = '1'");
        $this->assertEquals(1, $iRecordCount);
    }

    /**
     * Tests when empty articles array is given.
     */
    public function testResetArticlesWhenGivenEmptyArray()
    {
        $aArticles = array();
        /** @var CategoryVATGroupsPopulatorDbGateway $oDbGateway */
        $oDbGateway = oxNew(CategoryVATGroupsPopulatorDbGateway::class);

        $this->assertFalse($oDbGateway->reset($aArticles));
    }

    protected function _cleanFixtures()
    {
        \oxDb::getDb()->execute("DELETE FROM `oevattbe_articlevat` WHERE OEVATTBE_ARTICLEID = '1126'");
        \oxDb::getDb()->execute("DELETE FROM `oxarticles` WHERE OXID IN ('1126', '1127', '1131', '_testArticle')");
    }

    /**
     * Truncates database tables.
     */
    private function _cleanData()
    {
        \oxDb::getDb()->execute('TRUNCATE TABLE `oevattbe_articlevat`');
        \oxDb::getDb()->execute('TRUNCATE TABLE `oevattbe_categoryvat`');
        \oxDb::getDb()->execute('TRUNCATE TABLE `oxobject2category`');
        \oxDb::getDb()->execute('TRUNCATE TABLE `oxcategories`');
        \oxDb::getDb()->execute('TRUNCATE TABLE `oxarticles`');
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
            \oxDb::getDb()->execute($sSql);
        }
    }
}
