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
 *
 * @covers oeVATTBEOxArticle
 * @covers oeVATTBETBEArticleCacheKey
 */
class Unit_oeVATTBE_models_oeVATTBEOxArticleTest extends OxidTestCase
{
    /**
     * Test for vat tbe getter
     */
    public function testTbeVatGetter()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oevattbe_rate = new oxField(9);
        $this->assertSame(9, $oArticle->getTBEVat());
    }

    /**
     * Test for is tbe service
     */
    public function testIsTbeService()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oevattbe_istbeservice = new oxField(false);
        $this->assertFalse($oArticle->isTbeService());
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

        $this->assertSame('8.00', $oArticle->getTBEVat());
    }

    /**
     * Test case for loading article
     */
    public function testLoadArticleUserIsFromLocalCountry()
    {
        $this->_prepareData();

        $oUser = $this->getMock("oxUser", array("getTbeCountryId"));
        $oUser->expects($this->any())->method("getTbeCountryId")->will($this->returnValue(null));

        $oArticle = oxNew('oxArticle');
        $oArticle->setUser($oUser);

        $oArticle->load('1126');

        $this->assertNull($oArticle->getTBEVat());
    }

    /**
     * Test case for loading article
     */
    public function testLoadArticleUserNotLoggedIn()
    {
        $this->_prepareData();

        $oArticle = oxNew('oxArticle');
        $oArticle->load('1126');

        $this->assertNull($oArticle->getTBEVat());
    }

    /**
     * Test that module does not change behaviour when user is not logged in.
     */
    public function testGetCacheKeysWithoutActiveUser()
    {
        $oArticleWithoutModules = new oxArticle();
        $aCacheKeysWithoutModules = $oArticleWithoutModules->getCacheKeys();

        /** @var oxArticle $oArticle */
        $oArticle = oxNew('oxArticle');
        $aCacheKeys = $oArticle->getCacheKeys();
        $this->assertSame($aCacheKeysWithoutModules, $aCacheKeys);
    }

    /**
     * Test that module does not add user country for not TBE article when user is logged in.
     */
    public function testGetCacheKeysForNotTbeArticleWithActiveUser()
    {
        $sAustriaId = 'a7c40f6320aeb2ec2.72885259';
        /** @var oxUser $oUser */
        $oUser = $this->getMock('oxUser', array('getTbeCountryId'));
        $oUser->expects($this->any())->method('getTbeCountryId')->will($this->returnValue($sAustriaId));

        /** @var oxArticle $oArticle */
        $oArticle = oxNew('oxArticle');
        $oArticle->setUser($oUser);
        $aCacheKeys = $oArticle->getCacheKeys();

        $sShopId = $this->getConfig()->getShopId();
        $this->assertSame(array('oxArticle__'.$sShopId.'_de', 'oxArticle__'.$sShopId.'_en'), $aCacheKeys);
    }

    /**
     * Test that module add user country for TBE article when user is logged in.
     */
    public function testGetCacheKeysForTbeArticleWithActiveUser()
    {
        $sAustriaId = 'a7c40f6320aeb2ec2.72885259';
        /** @var oxUser $oUser */
        $oUser = $this->getMock('oxUser', array('getTbeCountryId'));
        $oUser->expects($this->any())->method('getTbeCountryId')->will($this->returnValue($sAustriaId));

        /** @var oxArticle $oArticle */
        $oArticle = oxNew('oxArticle');
        $oArticle->setUser($oUser);
        $oArticle->oxarticles__oevattbe_istbeservice = new oxField(true, oxField::T_RAW);
        $aCacheKeys = $oArticle->getCacheKeys();

        $sShopId = $this->getConfig()->getShopId();
        $this->assertSame(array('oxArticle__'.$sShopId.'_de_'. $sAustriaId, 'oxArticle__'.$sShopId.'_en_'. $sAustriaId), $aCacheKeys);
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
