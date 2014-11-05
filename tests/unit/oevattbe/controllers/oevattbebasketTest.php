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
 * Testing extended Basket controller.
 *
 * @covers oeVATTBEBasket
 */
class Unit_oeVATTBE_controllers_oeVATTBEBasketTest extends OxidTestCase
{

    /**
     * TBE Articles are in basket;
     * User is not logged in;
     * Marks message should be formed and domestic country should be set in message.
     */
    public function testGetOeVATTBEMarkMessageWhenUserIsNotLoggedIn()
    {
        $this->getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $this->getSession()->setBasket($oBasket);

        /** @var oeVATTBEBasket|Basket $oBasketController */
        $oBasketController = oxNew('oeVATTBEBasket');
        $oBasketController->setUser(null);

        $sExpectedMessage = '** - ';
        $sExpectedMessage .= sprintf(oxRegistry::getLang()->translateString('OEVATTBE_VAT_WILL_BE_CALCULATED_BY_USER_COUNTRY'), 'Deutschland');
        $this->assertEquals($sExpectedMessage, $oBasketController->getOeVATTBEMarkMessage());
    }

    /**
     * TBE Articles are in basket;
     * User is logged in;
     * User country is found;
     * Marks message should be formed and user country should be set in message.
     */
    public function testGetOeVATTBEMarkMessageWhenUserIsLoggedIn()
    {
        $this->getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');

        /** @var oxCountry|oeVATTBEOxCategory|PHPUnit_Framework_MockObject_MockObject $oCountry */
        $oCountry = $this->getMock("oeVATTBEOxCategory", array('getOeVATTBEName'));
        $oCountry->expects($this->any())->method("getOeVATTBEName")->will($this->returnValue('Deutschland'));

        /** @var oxBasket|oeVATTBEOxBasket|PHPUnit_Framework_MockObject_MockObject $oBasket */
        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'getOeVATTBECountry'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        $this->getSession()->setBasket($oBasket);

        /** @var oxUser|oeVATTBEOxUser $oUser */
        $oUser = oxNew('oxUser');

        $oBasketController = oxNew('oeVATTBEBasket');
        $oBasketController->setUser($oUser);

        $sExpectedMessage = '** - ';
        $sExpectedMessage .= sprintf(oxRegistry::getLang()->translateString('OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY'), 'Deutschland');
        $this->assertEquals($sExpectedMessage, $oBasketController->getOeVATTBEMarkMessage());
    }

    /**
     * TBE Articles are in basket;
     * User is logged in;
     * User country is not found;
     * Marks message should be formed and no country should be set in message.
     */
    public function testGetOeVATTBEMarkMessageWhenUserIsLoggedInAndUserCountryNotFound()
    {
        $this->getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');

        /** @var oxBasket|oeVATTBEOxBasket|PHPUnit_Framework_MockObject_MockObject $oBasket */
        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'getOeVATTBECountry'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue(null));
        $this->getSession()->setBasket($oBasket);

        /** @var oxUser|oeVATTBEOxUser $oUser */
        $oUser = oxNew('oxUser');

        $oBasketController = oxNew('oeVATTBEBasket');
        $oBasketController->setUser($oUser);

        $sExpectedMessage = '** - ';
        $sExpectedMessage .= sprintf(oxRegistry::getLang()->translateString('OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY'), '');
        $this->assertEquals($sExpectedMessage, $oBasketController->getOeVATTBEMarkMessage());
    }

    /**
     * Provider for testOeVATTBEShowVATTBEMarkMessage.
     *
     * @return array
     */
    public function providerShowVATTBEMarkMessageWhenMessageShouldBeHidden()
    {
        return array(
            array(true, true, true, true),
            array(false, true, true, false),
            array(false, false, true, true),
            array(false, false, false, false),
        );
    }

    /**
     * Tests showing of TBE mark message. Message should be shown depending on given parameters.
     *
     * @param bool $blIsDomesticCountry    Whether user country is domestic country
     * @param bool $blHasTBEArticles       Whether basket has TBE articles.
     * @param bool $blValidArticles        Is all basket articles valid.
     * @param bool $blCountryAppliesTBEVAT Whether country is configured as TBE country.
     *
     * @dataProvider providerShowVATTBEMarkMessageWhenMessageShouldBeHidden
     */
    public function testShowVATTBEMarkMessageWhenMessageShouldBeHidden($blIsDomesticCountry, $blHasTBEArticles, $blValidArticles, $blCountryAppliesTBEVAT)
    {
        $sDomesticCountryAbbr = $blIsDomesticCountry ? 'LT' : 'DE';
        $this->getConfig()->setConfigParam('sOeVATTBEDomesticCountry', $sDomesticCountryAbbr);
        $this->getSession()->setVariable('TBECountryId', '8f241f11095d6ffa8.86593236'); // LT

        $oCountry = $this->getMock("oeVATTBEOxCountry", array('appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue($blCountryAppliesTBEVAT));

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'isOeVATTBEValid', 'getOeVATTBECountry'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue($blHasTBEArticles));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue($blValidArticles));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        $this->getSession()->setBasket($oBasket);

        $oBasketController = oxNew('oeVATTBEBasket');
        $this->assertFalse($oBasketController->oeVATTBEShowVATTBEMarkMessage());
    }

    /**
     * User country does not match shop domestic country;
     * Basket has TBE articles;
     * TBE articles are valid (has VAT set);
     * User country is TBE country;
     * Marks message should be shown.
     */
    public function testShowVATTBEMarkMessageWhenMessageShouldBeShown()
    {
        $this->getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');
        $this->getSession()->setVariable('TBECountryId', '8f241f11095d6ffa8.86593236'); // LT

        $oCountry = $this->getMock("oeVATTBEOxCountry", array('appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(true));

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'isOeVATTBEValid', 'getOeVATTBECountry'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        $this->getSession()->setBasket($oBasket);

        $oBasketController = oxNew('oeVATTBEBasket');
        $this->assertTrue($oBasketController->oeVATTBEShowVATTBEMarkMessage());
    }
}
