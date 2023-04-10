<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\VisualCmsModule\Tests\Unit\Shop;

use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxUser class.
 *
 * @covers VatSelector
 */
class VatSelectorTest extends TestCase
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
