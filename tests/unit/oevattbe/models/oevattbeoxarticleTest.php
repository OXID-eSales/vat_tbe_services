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
class Unit_oeVatTbe_models_oeVatTbeOxArticleTest extends OxidTestCase
{
    /**
     * Test for vat tbe getter
     */
    public function testTbeVatGetter()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oevattbe_rate = new oxField(9);
        $this->assertSame(9, $oArticle->getTbeVat());
    }

    /**
     * Test case for loading article
     */
    public function testLoadArticle()
    {
        $this->_prepareData();

        $oUser = $this->getMock("oxUser", array("getTbeCountryId"));
        $oUser->expects($this->any())->method("getTbeCountryId")->will($this->returnValue('a7c40f631fc920687.20179984'));

        $oArticle = oxNew('oxArticle');
        $oArticle->setUser($oUser);

        $oArticle->load('1126');

        $this->assertSame('8.00', $oArticle->getTbeVat());
    }

    /**
     * Test case for loading article
     */
    public function testLoadArticleNoTbeCountry()
    {
        $this->_prepareData();

        $oUser = $this->getMock("oxUser", array("getTbeCountryId"));
        $oUser->expects($this->any())->method("getTbeCountryId")->will($this->returnValue(null));

        $oArticle = oxNew('oxArticle');
        $oArticle->setUser($oUser);

        $oArticle->load('1126');

        $this->assertNull($oArticle->getTbeVat());
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

