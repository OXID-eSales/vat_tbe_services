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
     * User is logged in;
     * User country was found;
     * User country is TBE country;
     * Marks (stars) should be set.
     */
    public function testGetTBEMarkMessageHasTBEArticleUserNotLoggedIn()
    {
        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasVATTBEArticles', 'getUser'));
        $oBasket->expects($this->any())->method("hasVATTBEArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getUser")->will($this->returnValue(null));

        $this->getSession()->setBasket($oBasket);

        $oBasketController = oxNew('oeVATTBEBasket');
        $this->assertStringEndsWith(oxRegistry::getLang()->translateString('OEVATTBE_VAT_WILL_BE_CALCULATED_BY_USER_COUNTRY'), $oBasketController->getTBEMarkMessage());
        $this->assertStringStartsWith('**', $oBasketController->getTBEMarkMessage());
    }

    /**
     * TBE Articles are in basket;
     * User is logged in;
     * User country was found;
     * User country is TBE country;
     * Marks (stars) should be set.
     */
    public function testGetTBEMarkMessageHasTBEArticleUserLoggedInBasketValidCountryTBE()
    {
        $oUser = oxNew("oxUser");

        $oCountry = $this->getMock("oxVATTBEoxCountry", array("appliesTBEVAT",'getVATTBEName'));
        $oCountry->expects($this->any())->method("appliesTBEVAT")->will($this->returnValue(true));
        $oCountry->expects($this->any())->method("getVATTBEName")->will($this->returnValue('LT'));

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasVATTBEArticles', 'getUser', 'getTBECountry'));
        $oBasket->expects($this->any())->method("hasVATTBEArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getUser")->will($this->returnValue($oUser));
        $oBasket->expects($this->any())->method("getTBECountry")->will($this->returnValue($oCountry));

        $this->getSession()->setBasket($oBasket);

        $oBasketController = oxNew('oeVATTBEBasket');
        $this->assertStringEndsWith(sprintf(oxRegistry::getLang()->translateString('OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY'), $oCountry->getVATTBEName()), $oBasketController->getTBEMarkMessage());
        $this->assertStringStartsWith('**', $oBasketController->getTBEMarkMessage());
    }

    /**
     * No TBE Articles are in basket;
     * Marks (stars) should not be set.
     */
    public function testGetTBEMarkMessageNoTBEArticleInBasket()
    {
        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasVATTBEArticles'));
        $oBasket->expects($this->any())->method("hasVATTBEArticles")->will($this->returnValue(false));

        $this->getSession()->setBasket($oBasket);

        $oBasketController = oxNew('oeVATTBEBasket');
        $this->assertSame('', $oBasketController->getTBEMarkMessage());
    }

    /**
     * TBE Articles are in basket;
     * User is not logged in;
     * Marks (stars) should not be set.
     */
    public function testGetTBEMarkMessageHasTBEArticleUserLoggedInBasketInvalid()
    {
        $oUser = oxNew("oxUser");

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasVATTBEArticles', 'getUser'));
        $oBasket->expects($this->any())->method("hasVATTBEArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getUser")->will($this->returnValue($oUser));

        $this->getSession()->setBasket($oBasket);

        $oBasketController = oxNew('oeVATTBEBasket');
        $this->assertSame('', $oBasketController->getTBEMarkMessage());
    }

    /**
     * TBE Articles are in basket;
     * User is logged in;
     * User country was not found;
     * Marks (stars) should not be set.
     */
    public function testGetTBEMarkMessageHasTBEArticleUserLoggedInBasketValidNoCountry()
    {
        $oUser = oxNew("oxUser");

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasVATTBEArticles', 'getUser', 'getTBECountry'));
        $oBasket->expects($this->any())->method("hasVATTBEArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getUser")->will($this->returnValue($oUser));
        $oBasket->expects($this->any())->method("getTBECountry")->will($this->returnValue(null));

        $this->getSession()->setBasket($oBasket);

        $oBasketController = oxNew('oeVATTBEBasket');
        $this->assertSame('', $oBasketController->getTBEMarkMessage());
    }

    /**
     * TBE Articles are in basket;
     * User is logged in;
     * User country was found;
     * User country is not TBE country;
     * Marks (stars) should not be set.
     */
    public function testGetTBEMarkMessageHasTBEArticleUserLoggedInBasketValidCountryDoNotTBE()
    {
        $oUser = oxNew("oxUser");

        $oCountry = $this->getMock("oxVATTBEoxCountry", array("appliesTBEVAT",'getVATTBEName'));
        $oCountry->expects($this->any())->method("appliesTBEVAT")->will($this->returnValue(false));

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasVATTBEArticles', 'getUser', 'getTBECountry'));
        $oBasket->expects($this->any())->method("hasVATTBEArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getUser")->will($this->returnValue($oUser));
        $oBasket->expects($this->any())->method("getTBECountry")->will($this->returnValue($oCountry));

        $this->getSession()->setBasket($oBasket);

        $oBasketController = oxNew('oeVATTBEBasket');
        $this->assertSame('', $oBasketController->getTBEMarkMessage());
    }

    public function testGetOeVATTBETBEVatFormatted()
    {
        $oFormatter = $this->getMock("oeVATTBEBasketItemVATFormatter", array("formatVAT"), array(), '', false);
        $oFormatter->expects($this->any())->method("formatVAT")->will($this->returnValue('12% **'));

        $oBasket = $this->getMock("oeVATTBEBasket", array("_getBasketItemVATFormatter"));
        $oBasket->expects($this->any())->method("_getBasketItemVATFormatter")->will($this->returnValue($oFormatter));

        $this->assertSame('12% **', $oBasket->getOeVATTBETBEVatFormatted(oxNew('oxBasketItem')));
    }
}
