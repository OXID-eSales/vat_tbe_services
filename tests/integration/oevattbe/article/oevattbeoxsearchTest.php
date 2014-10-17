<?php
/**
 * This file is part of OXID eSales VAT TBE module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales VAT TBE module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */


/**
 * Testing extended oxArticle class.
 */
class Integration_oeVATTBE_article_oeVATTBEOxSearchTest extends OxidTestCase
{
    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        $this->_prepareData();
    }

    /**
     * Search article list test
     */
    public function testGetSearchArticles()
    {
        $oUser = $this->getMock("oeVATTBEOxUser", array("getTbeCountryId"));
        $oUser->expects($this->any())->method("getTbeCountryId")->will($this->returnValue('a7c40f631fc920687.20179984'));

        $oSearch = oxNew("oeVATTBEOxSearch");
        $oSearch->setUser($oUser);

        $oArticleList = $oSearch->getSearchArticles('ABSINTH');

        /** @var oxArticle $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertSame('8.00', $oArticle->getTBEVat());
    }

    /**
     * Search article list test
     */
    public function testGetSearchArticlesUserCountryNotSet()
    {
        $oUser = $this->getMock("oeVATTBEOxUser", array("getTbeCountryId"));
        $oUser->expects($this->any())->method("getTbeCountryId")->will($this->returnValue(null));

        $oSearch = oxNew("oeVATTBEOxSearch");
        $oSearch->setUser($oUser);

        $oArticleList = $oSearch->getSearchArticles('ABSINTH');

        /** @var oxArticle $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertNull($oArticle->getTBEVat());
    }

    /**
     * Search article list test
     */
    public function testGetSearchArticlesUserNotLogged()
    {
        $oSearch = oxNew("oeVATTBEOxSearch");

        $oArticleList = $oSearch->getSearchArticles('ABSINTH');

        /** @var oxArticle $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertNull($oArticle->getTBEVat());
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
