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
     * User country was found;
     * User country is TBE country;
     * Marks (stars) should be set.
     */
    public function testGetOeVATTBEMarkMessageHasTBEArticleUserNotLoggedIn()
    {
        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'getUser'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getUser")->will($this->returnValue(null));

        $this->getSession()->setBasket($oBasket);

        $oBasketController = oxNew('oeVATTBEBasket');
        $this->assertStringEndsWith(sprintf(oxRegistry::getLang()->translateString('OEVATTBE_VAT_WILL_BE_CALCULATED_BY_USER_COUNTRY'), 'Deutschland'), $oBasketController->getOeVATTBEMarkMessage());
        $this->assertStringStartsWith('**', $oBasketController->getOeVATTBEMarkMessage());
    }

    /**
     * TBE Articles are in basket;
     * User is not logged in;
     * User country was found;
     * User country is TBE country;
     * Marks (stars) should be set.
     */
    public function testGetOeVATTBEMarkMessageHasTBEArticleUserNotLoggedInBadDomesticCountry()
    {
        oxRegistry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'blabla');

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'getUser'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getUser")->will($this->returnValue(null));

        $this->getSession()->setBasket($oBasket);

        $oBasketController = oxNew('oeVATTBEBasket');
        $this->assertStringEndsWith(sprintf(oxRegistry::getLang()->translateString('OEVATTBE_VAT_WILL_BE_CALCULATED_BY_USER_COUNTRY'), ''), $oBasketController->getOeVATTBEMarkMessage());
        $this->assertStringStartsWith('**', $oBasketController->getOeVATTBEMarkMessage());
    }

    /**
     * TBE Articles are in basket;
     * User is logged in;
     * User country was found;
     * User country is TBE country;
     * Marks (stars) should be set.
     */
    public function testGetOeVATTBEMarkMessageHasTBEArticleUserLoggedInBasketValidCountryTBE()
    {
        $oUser = oxNew("oxUser");

        $oCountry = $this->getMock("oeVATTBEOxCountry", array("appliesOeTBEVATTbeVat",'getOeVATTBEName'));
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(true));
        $oCountry->expects($this->any())->method("getOeVATTBEName")->will($this->returnValue('LT'));

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'getUser', 'getOeVATTBECountry'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getUser")->will($this->returnValue($oUser));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));

        $this->getSession()->setBasket($oBasket);

        $oBasketController = oxNew('oeVATTBEBasket');
        $this->assertStringEndsWith(sprintf(oxRegistry::getLang()->translateString('OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY'), $oCountry->getOeVATTBEName()), $oBasketController->getOeVATTBEMarkMessage());
        $this->assertStringStartsWith('**', $oBasketController->getOeVATTBEMarkMessage());
    }

    /**
     * No TBE Articles are in basket;
     * Marks (stars) should not be set.
     */
    public function testGetOeVATTBEMarkMessageNoTBEArticleInBasket()
    {
        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(false));

        $this->getSession()->setBasket($oBasket);

        $oBasketController = oxNew('oeVATTBEBasket');
        $this->assertSame('', $oBasketController->getOeVATTBEMarkMessage());
    }

    /**
     * TBE Articles are in basket;
     * User is logged in;
     * Marks (stars) should not be set.
     */
    public function testGetOeVATTBEMarkMessageHasTBEArticleUserLoggedInBasketInvalid()
    {
        $oUser = oxNew("oxUser");

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'getUser'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getUser")->will($this->returnValue($oUser));

        $this->getSession()->setBasket($oBasket);

        $oBasketController = oxNew('oeVATTBEBasket');
        $this->assertSame('', $oBasketController->getOeVATTBEMarkMessage());
    }

    /**
     * TBE Articles are in basket;
     * User is logged in;
     * User country was not found;
     * Marks (stars) should not be set.
     */
    public function testGetOeVATTBEMarkMessageHasTBEArticleUserLoggedInBasketValidNoCountry()
    {
        $oUser = oxNew("oxUser");

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'getUser', 'getOeVATTBECountry'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getUser")->will($this->returnValue($oUser));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue(null));

        $this->getSession()->setBasket($oBasket);

        $oBasketController = oxNew('oeVATTBEBasket');
        $this->assertSame('', $oBasketController->getOeVATTBEMarkMessage());
    }

    /**
     * TBE Articles are in basket;
     * User is logged in;
     * User country was found;
     * User country is not TBE country;
     * Marks (stars) should not be set.
     */
    public function testGetOeVATTBEMarkMessageHasTBEArticleUserLoggedInBasketValidCountryDoNotTBE()
    {
        $oUser = oxNew("oxUser");

        $oCountry = $this->getMock("oeVATTBEOxCountry", array("appliesOeTBEVATTbeVat"));
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(false));

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'getUser', 'getOeVATTBECountry'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getUser")->will($this->returnValue($oUser));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));

        $this->getSession()->setBasket($oBasket);

        $oBasketController = oxNew('oeVATTBEBasket');
        $this->assertSame('', $oBasketController->getOeVATTBEMarkMessage());
    }
}
