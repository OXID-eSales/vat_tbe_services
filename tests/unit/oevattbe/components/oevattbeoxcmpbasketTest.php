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
 * @covers oeVATTBEOxCmp_Basket
 */
class Unit_oeVatTbe_components_oeVATTBEOxCmpBasketTest extends OxidTestCase
{
    /**
     * Render test
     */
    public function testRenderBasketWithoutTbeCountry()
    {
        $oCountry = $this->getMock("oeVATTBEOxCountry", array("appliesTBEVAT"));
        $oCountry->expects($this->any())->method("appliesTBEVAT")->will($this->returnValue(true));

        $oUser = $this->getMock("oeVATTBEOxUser", array("getOeVATTBETbeCountryId", 'hasOeTBEVATArticles'));
        $oUser->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue('DE'));

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'getTBECountry'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getTBECountry")->will($this->returnValue($oCountry));

        $this->getSession()->setBasket($oBasket);

        $oCmp_Basket = oxNew('oxCmp_Basket');
        $oCmp_Basket->setUser($oUser);

        $oBasket = $oCmp_Basket->render();

        $this->assertSame('DE', $oBasket->getOeVATTBETbeCountryId());
        $this->assertTrue($oBasket->showTBECountryChangedError());
    }

    /**
     * Render test
     */
    public function testRenderBasketWithTbeCountry()
    {
        $oCountry = $this->getMock("oeVATTBEOxCountry", array("appliesTBEVAT"));
        $oCountry->expects($this->any())->method("appliesTBEVAT")->will($this->returnValue(true));

        $oUser = $this->getMock("oeVATTBEOxUser", array("getOeVATTBETbeCountryId", 'hasOeTBEVATArticles'));
        $oUser->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue('DE'));

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'getTBECountry'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getTBECountry")->will($this->returnValue($oCountry));
        $oBasket->setOeVATTBECountryId('LT');

        $this->getSession()->setBasket($oBasket);

        $oCmp_Basket = oxNew('oxCmp_Basket');
        $oCmp_Basket->setUser($oUser);

        $oBasket = $oCmp_Basket->render();

        $this->assertSame('DE', $oBasket->getOeVATTBETbeCountryId());
        $this->assertTrue($oBasket->showTBECountryChangedError());
    }

    /**
     * Render test
     */
    public function testRenderBasketWithNotTbeCountry()
    {
        $oCountry = $this->getMock("oeVATTBEOxCountry", array("appliesTBEVAT"));
        $oCountry->expects($this->any())->method("appliesTBEVAT")->will($this->returnValue(false));

        $oUser = $this->getMock("oeVATTBEOxUser", array("getOeVATTBETbeCountryId", 'hasOeTBEVATArticles'));
        $oUser->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue('DE'));

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'getTBECountry'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getTBECountry")->will($this->returnValue($oCountry));
        $oBasket->setOeVATTBECountryId('LT');

        $this->getSession()->setBasket($oBasket);

        $oCmp_Basket = oxNew('oxCmp_Basket');
        $oCmp_Basket->setUser($oUser);

        $oBasket = $oCmp_Basket->render();

        $this->assertSame('DE', $oBasket->getOeVATTBETbeCountryId());
        $this->assertFalse($oBasket->showTBECountryChangedError());
    }



    /**
     * Render test
     */
    public function testRenderBasketWithTbeCountryNoTBEArticles()
    {
        $oUser = $this->getMock("oeVATTBEOxUser", array("getOeVATTBETbeCountryId", 'hasOeTBEVATArticles'));
        $oUser->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue('DE'));

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(false));
        $oBasket->setOeVATTBECountryId('LT');

        $this->getSession()->setBasket($oBasket);

        $oCmp_Basket = oxNew('oxCmp_Basket');
        $oCmp_Basket->setUser($oUser);

        $oBasket = $oCmp_Basket->render();

        $this->assertSame('DE', $oBasket->getOeVATTBETbeCountryId());
        $this->assertFalse($oBasket->showTBECountryChangedError());
    }

    /**
     * Render test
     */
    public function testRenderBasketWithTbeSameCountry()
    {
        $oUser = $this->getMock("oeVATTBEOxUser", array("getOeVATTBETbeCountryId", 'hasOeTBEVATArticles'));
        $oUser->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue('DE'));

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->setOeVATTBECountryId('DE');

        $this->getSession()->setBasket($oBasket);

        $oCmp_Basket = oxNew('oxCmp_Basket');
        $oCmp_Basket->setUser($oUser);

        $oBasket = $oCmp_Basket->render();

        $this->assertSame('DE', $oBasket->getOeVATTBETbeCountryId());
        $this->assertFalse($oBasket->showTBECountryChangedError());
    }
}
