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
 * Testing extended oxUser class.
 *
 * @covers oeVATTBEOxVatSelector
 */
class Unit_oeVatTbe_models_oeVATTBEOxVatSelectorTest extends OxidTestCase
{

    public function providerArticleUserVatCalculationWhenHasTbeVatAndIsTbeArticle()
    {
        return array(
            array(100),
            array(19),
            array(0),
            array(-25),
        );
    }

    /**
     * @param int $iVat
     *
     * @dataProvider providerArticleUserVatCalculationWhenHasTbeVatAndIsTbeArticle
     */
    public function testArticleUserVatCalculationWhenHasTbeVatAndIsTbeArticle($iVat)
    {
        $oArticle = $this->getMock('oeVatTbeOxArticle', array('getOeVATTBETBEVat', 'isOeVATTBETBEService'));
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->will($this->returnValue($iVat));
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->will($this->returnValue(true));

        $oVatSelector = oxNew('oxVatSelector');

        $this->assertSame($iVat, $oVatSelector->getArticleUserVat($oArticle));
    }

    /**
     * When article does not have TBE VAT calculated but is TBE article, it should fall back
     * to parent's VAT calculation. As in this case user is not logged in - false is returned.
     */
    public function testArticleUserVatCalculationWhenTbeVatNotSetAndIsTbeArticle()
    {
        $oArticle = $this->getMock('oeVatTbeOxArticle', array('getOeVATTBETBEVat', 'isOeVATTBETBEService'));
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->will($this->returnValue(null));
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->will($this->returnValue(true));

        $oVatSelector = oxNew('oxVatSelector');

        $this->assertSame(false, $oVatSelector->getArticleUserVat($oArticle));
    }

    /**
     * When article does have TBE VAT calculated but is not TBE article, it should fall back
     * to parent's VAT calculation. As in this case user is not logged in - false is returned.
     */
    public function testArticleUserVatCalculationWhenTbeVatSetAndIsNotTbeArticle()
    {
        $oArticle = $this->getMock('oeVatTbeOxArticle', array('getOeVATTBETBEVat', 'isOeVATTBETBEService'));
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->will($this->returnValue(15));
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->will($this->returnValue(false));

        $oVatSelector = oxNew('oxVatSelector');

        $this->assertSame(false, $oVatSelector->getArticleUserVat($oArticle));
    }

    /**
     * Test VAT calculation when article is TBE service, but there is admin mode tuned on.
     */
    public function testArticleUserVatCalculationWhenIsAdmin()
    {
        $oArticle = $this->getMock('oeVatTbeOxArticle', array('getOeVATTBETBEVat', 'isOeVATTBETBEService'));
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->will($this->returnValue(15));
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->will($this->returnValue(true));
        $this->setAdminMode(true);

        $oVatSelector = oxNew('oxVatSelector');

        $this->assertSame(false, $oVatSelector->getArticleUserVat($oArticle));
    }

    /**
     * Test VAT calculation when article is TBE service, but user is from shop's domestic country.
     */
    public function testArticleUserVatCalculationWhenUserFromDomesticCountry()
    {
        $this->getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');
        $this->getSession()->setVariable('TBECountryId', 'a7c40f631fc920687.20179984');
        $oArticle = $this->getMock('oeVatTbeOxArticle', array('getOeVATTBETBEVat', 'isOeVATTBETBEService'));
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->will($this->returnValue(15));
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->will($this->returnValue(true));

        $oVatSelector = oxNew('oxVatSelector');

        $this->assertSame(false, $oVatSelector->getArticleUserVat($oArticle));
    }

    /**
     * Test VAT calculation when article is TBE service and user is not from shop's domestic country.
     */
    public function testArticleUserVatCalculationWhenUserNotFromDomesticCountry()
    {
        $this->getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'LT');
        $this->getSession()->setVariable('TBECountryId', 'a7c40f631fc920687.20179984');
        $oArticle = $this->getMock('oeVatTbeOxArticle', array('getOeVATTBETBEVat', 'isOeVATTBETBEService'));
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->will($this->returnValue(15));
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->will($this->returnValue(true));

        $oVatSelector = oxNew('oxVatSelector');

        $this->assertSame(15, $oVatSelector->getArticleUserVat($oArticle));
    }

    /**
     * Test if VAT applied for not business customer
     * when article is TBE service and user is not from shop's domestic country.
     */
    public function testGetArticleForNotBusinessCustomer()
    {
        /** @var oxUser $oUser */
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxustid = new oxField('0');

        $this->getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'LT');
        $this->getSession()->setVariable('TBECountryId', 'a7c40f631fc920687.20179984');
        $oArticle = $this->getMock('oeVatTbeOxArticle', array('getOeVATTBETBEVat', 'isOeVATTBETBEService'));
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->will($this->returnValue(15));
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->will($this->returnValue(true));
        $oArticle->setArticleUser($oUser);

        /** @var oeVATTBEOxVatSelector $oVatSelector */
        $oVatSelector = oxNew('oeVATTBEOxVatSelector');

        $this->assertSame(15, $oVatSelector->getArticleUserVat($oArticle));
    }

    /**
     * Test if 0 VAT applied for business customer
     * when article is TBE service and user is not from shop's domestic country.
     */
    public function testGetArticleForBusinessCustomer()
    {
        /** @var oxUser $oUser */
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxustid = new oxField('1');

        $this->getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'LT');
        $this->getSession()->setVariable('TBECountryId', 'a7c40f631fc920687.20179984');
        $oArticle = $this->getMock('oeVatTbeOxArticle', array('getOeVATTBETBEVat', 'isOeVATTBETBEService'));
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->will($this->returnValue(15));
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->will($this->returnValue(true));
        $oArticle->setArticleUser($oUser);

        /** @var oeVATTBEOxVatSelector $oVatSelector */
        $oVatSelector = oxNew('oeVATTBEOxVatSelector');

        $this->assertNotSame(15, $oVatSelector->getArticleUserVat($oArticle));
        $this->assertEquals(false, $oVatSelector->getArticleUserVat($oArticle));
    }
}
