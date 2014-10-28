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
 * Testing extended Order controller.
 *
 * @covers oeVATTBEOrder
 */
class Unit_oeVATTBE_controllers_oeVATTBEOrderTest extends OxidTestCase
{
    public function testGetTBEMarkMessageNoTBEArticleInBasket()
    {
        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasVATTBEArticles'));
        $oBasket->expects($this->any())->method("hasVATTBEArticles")->will($this->returnValue(false));

        $this->getSession()->setBasket($oBasket);

        $oOrder = oxNew('oeVATTBEOrder');
        $this->assertSame('', $oOrder->getTBEMarkMessage());
    }

    public function testGetTBEMarkMessageHasTBEArticleInBasketButInvalid()
    {
        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasVATTBEArticles', 'isTBEValid'));
        $oBasket->expects($this->any())->method("hasVATTBEArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("isTBEValid")->will($this->returnValue(false));

        $this->getSession()->setBasket($oBasket);

        $oOrder = oxNew('oeVATTBEOrder');
        $this->assertSame('', $oOrder->getTBEMarkMessage());
    }

    public function testGetTBEMarkMessageHasTBEArticleInBasketValidButNoCountry()
    {
        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasVATTBEArticles', 'isTBEValid', 'getTBECountry'));
        $oBasket->expects($this->any())->method("hasVATTBEArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("isTBEValid")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getTBECountry")->will($this->returnValue(null));

        $this->getSession()->setBasket($oBasket);

        $oOrder = oxNew('oeVATTBEOrder');
        $this->assertSame('', $oOrder->getTBEMarkMessage());
    }

    public function testGetTBEMarkMessageHasTBEArticleInBasketValidCountryNotTBE()
    {
        $oCountry = $this->getMock("oeVATTBEOxCountry", array("appliesTBEVAT"));
        $oCountry->expects($this->any())->method("appliesTBEVAT")->will($this->returnValue(false));

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasVATTBEArticles', 'isTBEValid', 'getTBECountry'));
        $oBasket->expects($this->any())->method("hasVATTBEArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("isTBEValid")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getTBECountry")->will($this->returnValue($oCountry));

        $this->getSession()->setBasket($oBasket);

        $oOrder = oxNew('oeVATTBEOrder');
        $this->assertSame('', $oOrder->getTBEMarkMessage());
    }

    public function testGetTBEMarkMessageHasTBEArticleInBasketValidCountryTBE()
    {
        $oCountry = $this->getMock("oeVATTBEOxCountry", array("appliesTBEVAT",'getVATTBEName'));
        $oCountry->expects($this->any())->method("appliesTBEVAT")->will($this->returnValue(true));
        $oCountry->expects($this->any())->method("getVATTBEName")->will($this->returnValue('LT'));

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasVATTBEArticles', 'isTBEValid', 'getTBECountry'));
        $oBasket->expects($this->any())->method("hasVATTBEArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("isTBEValid")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getTBECountry")->will($this->returnValue($oCountry));

        $this->getSession()->setBasket($oBasket);

        $oOrder = oxNew('oeVATTBEOrder');

        $this->assertStringEndsWith(sprintf(oxRegistry::getLang()->translateString('OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY'), $oCountry->getVATTBEName()), $oOrder->getTBEMarkMessage());
        $this->assertStringStartsWith('**', $oOrder->getTBEMarkMessage());
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
