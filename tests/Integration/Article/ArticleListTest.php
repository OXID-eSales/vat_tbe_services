<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Article;

use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxArticle class.
 */
class ArticleListTest extends TestCase
{
    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setup();
        $this->_prepareData();
    }

    /**
     * data provider
     *
     * @return array
     */
    public function userConfiguration()
    {
        return array(
            //array( 'local', null ),
            array( 'notLoggedIn', null ),
            array( 'loggedIn', '8.00' ),
            array( 'loggedInWithoutCountry', null )
        );
    }

    /**
     * Category list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testCategoryList($sUserStatus, $sVat)
    {
        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadCategoryArticles('30e44ab8593023055.23928895', null);

        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf('oxArticle', $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Category list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testManufacturerList($sUserStatus, $sVat)
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('1126');
        $oArticle->oxarticles__oxmanufacturerid = new oxField('manufacturerId');
        $oArticle->save();

        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadManufacturerArticles('manufacturerId');

        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf('oxArticle', $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Category list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testVendorList($sUserStatus, $sVat)
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('1126');
        $oArticle->oxarticles__oxvendorid = new oxField('vendorId');
        $oArticle->save();

        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadVendorArticles('vendorId');

        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf('oxArticle', $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Category list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testPriceCategoryList($sUserStatus, $sVat)
    {
        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadPriceArticles(20, 40);

        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf('oxArticle', $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Action list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testActionList($sUserStatus, $sVat)
    {
        $oArticle2Action = oxNew('oxbase');
        $oArticle2Action->init('oxactions2article');
        $oArticle2Action->oxactions2article__oxactionid = new oxField('oxstart');
        $oArticle2Action->oxactions2article__oxartid = new oxField('1126');
        $oArticle2Action->save();

        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadActionArticles('oxstart');

        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf('oxArticle', $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Newest list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testNewestList($sUserStatus, $sVat)
    {
        $this->getConfig()->setConfigParam('iNewestArticlesMode', 2);
        $this->getConfig()->setConfigParam('blNewArtByInsert', false);

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('1126');
        $oArticle->oxarticles__oxtimestamp = new oxField(date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime()));
        $oArticle->save();

        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadNewestArticles();

        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf('oxArticle', $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Top 5 list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testTop5Articles($sUserStatus, $sVat)
    {
        $this->getConfig()->setConfigParam('iTop5Mode', 2);

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('1126');
        $oArticle->oxarticles__oxsoldamount = new oxField(9999);
        $oArticle->save();

        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadTop5Articles();

        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf('oxArticle', $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Cross selling list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testArticleCrossSell($sUserStatus, $sVat)
    {
        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadArticleCrossSell('1964');

        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf('oxArticle', $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Article accessoires list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testArticleAccessoires($sUserStatus, $sVat)
    {
        $oAccessoire2article = oxNew("oxBase");
        $oAccessoire2article->init("oxaccessoire2article");
        $oAccessoire2article->oxaccessoire2article__oxobjectid = new oxField('1126');
        $oAccessoire2article->oxaccessoire2article__oxarticlenid = new oxField('1964');
        $oAccessoire2article->save();

        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadArticleAccessoires('1964');

        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf('oxArticle', $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Recommendation list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testloadRecommArticles($sUserStatus, $sVat)
    {
        $oObject2list = oxNew("oxBase");
        $oObject2list->init("oxobject2list");
        $oObject2list->oxobject2list__oxobjectid = new oxField('1126');
        $oObject2list->oxobject2list__oxlistid = new oxField('list');
        $oObject2list->save();

        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadRecommArticles('list');

        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf('oxArticle', $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Test forming of articles list in admin when user country is found but shop is in admin mode.
     */
    public function testLoadingArticlesInAdminWithFilter()
    {
        $this->getSession()->setVariable('TBECountryId', 'a7c40f631fc920687.20179984');
        $this->setConfigParam('art_category', 'cat@@30e44ab8593023055.23928895');
        $oArticleList = oxNew("Article_List");
        $oArticleList->setAdminMode(true);
        $aListItems = $oArticleList->getItemList();

        $this->assertSame(9, count($aListItems));
    }

    /**
     * prepare data
     *
     */
    protected function _prepareData()
    {
        $oDb = oxDb::getDb();
        $oDb->execute("TRUNCATE TABLE oevattbe_countryvatgroups");
        $oDb->execute("TRUNCATE TABLE oevattbe_articlevat");
        $oDb->execute("DELETE FROM  `oxobject2category` WHERE `OXID`='c3944abfcb65b13a3.66180278'");
        $sql = "INSERT INTO oevattbe_countryvatgroups SET OEVATTBE_ID = 1, OEVATTBE_COUNTRYID = 'a7c40f631fc920687.20179984', OEVATTBE_NAME='name', OEVATTBE_RATE='8'";
        $oDb->execute($sql);
        $sql = "INSERT INTO oevattbe_articlevat SET OEVATTBE_ARTICLEID = '1126', OEVATTBE_COUNTRYID = 'a7c40f631fc920687.20179984', OEVATTBE_VATGROUPID = '1'";
        $oDb->execute($sql);
        $sql = "INSERT INTO `oxobject2category` (`OXID`, `OXOBJECTID`, `OXCATNID`, `OXPOS`, `OXTIME`) VALUES
        ('c3944abfcb65b13a3.66180278', '1126', '30e44ab8593023055.23928895', 0, 1152122038)";
        $oDb->execute($sql);
    }

    /**
     * Prepare article list object for testing
     *
     * @param string $sUserStatus user status
     *
     * @return oxArticleList
     */
    protected function _getArticleList($sUserStatus = 'notLoggedIn')
    {
        $oArticleList = oxNew("oxArticleList");
        $oArticle = $oArticleList->getBaseObject();

        if ($sUserStatus != 'notLoggedIn') {
            $oUser = $this->getMock("oeVATTBEOxUser", array("getOeVATTBETbeCountryId"));
            $sCountryId = ($sUserStatus == 'loggedInWithoutCountry') ? null : 'a7c40f631fc920687.20179984';
            $oUser->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue($sCountryId));
            $oArticle->setUser($oUser);
        }

        return $oArticleList;
    }
}
