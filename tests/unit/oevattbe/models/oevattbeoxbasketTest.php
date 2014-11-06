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
 * Testing extended oxArticle class.
 *
 * @covers oeVATTBEOxBasket
 */
class Unit_oeVATTBE_models_oeVATTBEOxBasketTest extends OxidTestCase
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
        $this->getConfig()->setConfigParam('sOeVATTBEDomesticCountry', $sDomesticCountry);
        $this->getSession()->setVariable('TBECountryId', $sLithuaniaId);

        /** @var oxCountry $oCountry */
        $oCountry = oxNew('oxCountry');
        $oCountry->load($sLithuaniaId);
        $oCountry->oxcountry__oevattbe_appliestbevat = new oxField($blTBECountry);
        $oCountry->save();

        /** @var oxArticle $oArticle */
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArticle1');
        $oArticle->oxarticles__oevattbe_istbeservice = new oxField($blIsArticleTbeService);
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
        $this->getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');
        $sLithuaniaId = '8f241f11095d6ffa8.86593236';
        $this->getSession()->setVariable('TBECountryId', $sLithuaniaId); // LT

        /** @var oxCountry $oCountry */
        $oCountry = oxNew('oxCountry');
        $oCountry->setId($sLithuaniaId);
        $oCountry->oxcountry__oevattbe_appliestbevat = new oxField(true);
        $oCountry->save();

        /** @var oxArticle $oArticle */
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArticle1');
        $oArticle->oxarticles__oevattbe_istbeservice = new oxField(true);
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
        $this->assertSame('Deutschland', $oBasket->getOeVATTBECountry()->oxcountry__oxtitle->value);
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
