<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Shop;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxArticle class.
 *
 * @covers Basket
 */
class BasketTest extends TestCase
{
    /**
     * Test for tbe country id setter and getter
     */
    public function testSetgetOeVATTBETbeCountryId()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->setOeVATTBECountryId('de');
        $this->assertSame('de', $oBasket->getOeVATTBETbeCountryId());
    }

    /**
     * Test get country when it is not set
     */
    public function testGetOeVATTBETbeCountryIdNotSet()
    {
        $oBasket = oxNew('oxBasket');
        $this->assertNull($oBasket->getOeVATTBECountry());
    }

    /**
     * Data provider for testSetCountryIdOnChangeEvent test.
     *
     * @return array
     */
    public function providerSetCountryIdOnChangeEvent()
    {
        return array(
            array(true, true, true),
            array(false, false, true),
            array(false, false, false),
        );
    }

    /**
     * Test on basket country change event when no message should be shown after country change.
     *
     * @param bool $blDomesticCountry     Is user from shops domestic country.
     * @param bool $blTBECountry          Is user country TBE country.
     * @param bool $blIsArticleTbeService Is basket article TBE service.
     *
     * @dataProvider providerSetCountryIdOnChangeEvent
     */
    public function testSetCountryIdOnChangeEvent($blDomesticCountry, $blTBECountry, $blIsArticleTbeService)
    {
        $sDomesticCountry = $blDomesticCountry ? 'LT' : 'DE';
        $sLithuaniaId = '8f241f11095d6ffa8.86593236';
        Registry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', $sDomesticCountry);
        Registry::getSession()->setVariable('TBECountryId', $sLithuaniaId);

        /** @var oxCountry $oCountry */
        $oCountry = oxNew('oxCountry');
        $oCountry->load($sLithuaniaId);
        $oCountry->oxcountry__oevattbe_appliestbevat = new Field($blTBECountry);
        $oCountry->save();

        /** @var oxArticle $oArticle */
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArticle1');
        $oArticle->oxarticles__oevattbe_istbeservice = new Field($blIsArticleTbeService);
        $oArticle->save();

        /** @var oxBasket|oeVATTBEOxBasket $oBasket */
        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket('_testArticle1', 1);
        $oBasket->setOeVATTBECountryId($sLithuaniaId);

        $this->assertFalse($oBasket->showOeVATTBECountryChangedError());
    }

    /**
     * Provides information if need to add article to basket or leave it empty.
     *
     * @return array
     */
    public function providerSetCountryIdOnChangeEventWhenMessageShouldBeShown()
    {
        return array(
            array(true),
            array(false),
        );
    }

    /**
     * Test on basket country change event when message should be shown after country change.
     *
     * @param bool $bAddToBasket if some article are in basket.
     *
     * @dataProvider providerSetCountryIdOnChangeEventWhenMessageShouldBeShown
     */
    public function testSetCountryIdOnChangeEventWhenMessageShouldBeShown($bAddToBasket)
    {
        Registry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');
        $sLithuaniaId = '8f241f11095d6ffa8.86593236';
        Registry::getSession()->setVariable('TBECountryId', $sLithuaniaId); // LT

        /** @var oxCountry $oCountry */
        $oCountry = oxNew('oxCountry');
        $oCountry->setId($sLithuaniaId);
        $oCountry->oxcountry__oevattbe_appliestbevat = new Field(true);
        $oCountry->save();

        /** @var oxArticle $oArticle */
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArticle1');
        $oArticle->oxarticles__oevattbe_istbeservice = new Field(true);
        $oArticle->save();

        /** @var oxBasket|oeVATTBEOxBasket $oBasket */
        $oBasket = oxNew('oxBasket');
        $oBasket->setOeVATTBECountryId($sLithuaniaId);
        if ($bAddToBasket) {
            $oBasket->addToBasket('_testArticle1', 1);
        }

        $this->assertTrue($oBasket->showOeVATTBECountryChangedError());
    }

    /**
     * Test get country when it is set
     */
    public function testGetOeVATTBETbeCountryIdSet()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->setOeVATTBECountryId('a7c40f631fc920687.20179984');
        $this->assertSame('Deutschland', $oBasket->getOeVATTBECountry()->getFieldData('oxtitle'));
    }

    /**
     * Show error default value
     */
    public function testShowOeVATTBECountryChangedErrorDefault()
    {
        $oBasket = oxNew('oxBasket');
        $this->assertFalse($oBasket->showOeVATTBECountryChangedError());
    }

    /**
     * Show error after set and show one time
     */
    public function testShowOeVATTBECountryChangedErrorShow()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->setOeVATTBECountryChanged();
        $this->assertTrue($oBasket->showOeVATTBECountryChangedError());
        $this->assertFalse($oBasket->showOeVATTBECountryChangedError());
    }

    /**
     * test for basket validation
     */
    public function testisOeVATTBEValidValid()
    {
        $oChecker = $this->getMock('oeVATTBEOrderArticleChecker', array('isValid'), array(), '', false);
        $oChecker->expects($this->any())->method('isValid')->will($this->returnValue(true));

        $oBasket = $this->getMock('oeVATTBEOxBasket', array('_getOeVATTBEOrderArticleChecker'));
        $oBasket->expects($this->any())->method('_getOeVATTBEOrderArticleChecker')->will($this->returnValue($oChecker));

        $this->assertTrue($oBasket->isOeVATTBEValid());
    }

    /**
     * test for basket validation
     */
    public function testisOeVATTBEValidNotValid()
    {
        $oChecker = $this->getMock('oeVATTBEOrderArticleChecker', array('isValid'), array(), '', false);
        $oChecker->expects($this->any())->method('isValid')->will($this->returnValue(false));

        $oBasket = $this->getMock('oeVATTBEOxBasket', array('_getOeVATTBEOrderArticleChecker'));
        $oBasket->expects($this->any())->method('_getOeVATTBEOrderArticleChecker')->will($this->returnValue($oChecker));

        $this->assertFalse($oBasket->isOeVATTBEValid());
    }
}
