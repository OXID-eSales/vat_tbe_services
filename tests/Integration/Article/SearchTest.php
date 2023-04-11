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
class SearchTest extends TestCase
{
    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        $this->_prepareData();
        parent::setup();
    }

    /**
     * Search article list test
     */
    public function testGetSearchArticles()
    {
        $oUser = $this->getMock("oeVATTBEOxUser", array("getOeVATTBETbeCountryId"));
        $oUser->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue('a7c40f631fc920687.20179984'));

        $oSearch = oxNew("oxSearch");
        $oSearch->setUser($oUser);

        $oArticleList = $oSearch->getSearchArticles('ABSINTH');

        /** @var oxArticle $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf('oxArticle', $oArticle);
        $this->assertSame('8.00', $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Search article list test
     */
    public function testGetSearchArticlesUserCountryNotSet()
    {
        $oUser = $this->getMock("oeVATTBEOxUser", array("getOeVATTBETbeCountryId"));
        $oUser->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue(null));

        $oSearch = oxNew("oxSearch");
        $oSearch->setUser($oUser);

        $oArticleList = $oSearch->getSearchArticles('ABSINTH');

        /** @var oxArticle $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertNull($oArticle->getOeVATTBETBEVat());
    }

    /**
     * Search article list test
     */
    public function testGetSearchArticlesUserNotLogged()
    {
        $oSearch = oxNew("oxSearch");

        $oArticleList = $oSearch->getSearchArticles('ABSINTH');

        /** @var oxArticle $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertNull($oArticle->getOeVATTBETBEVat());
    }

    /**
     * prepare data
     */
    protected function _prepareData()
    {
        $oDb = oxDb::getDb();

        $oDb->execute("TRUNCATE TABLE oevattbe_countryvatgroups");
        $oDb->execute("TRUNCATE TABLE oevattbe_articlevat");

        $sql = "INSERT INTO oevattbe_countryvatgroups SET OEVATTBE_ID = 1, OEVATTBE_COUNTRYID = 'a7c40f631fc920687.20179984', OEVATTBE_NAME='name', OEVATTBE_RATE='8'";

        $oDb->execute($sql);

        $sql = "INSERT INTO oevattbe_articlevat SET OEVATTBE_ARTICLEID = '1126', OEVATTBE_COUNTRYID = 'a7c40f631fc920687.20179984', OEVATTBE_VATGROUPID = '1'";

        $oDb->execute($sql);
    }
}
