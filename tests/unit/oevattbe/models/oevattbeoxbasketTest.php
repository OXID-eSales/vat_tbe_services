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
    public function testShowTBECountryChangedErrorDefault()
    {
        $oBasket = oxNew('oxBasket');
        $this->assertFalse($oBasket->showTBECountryChangedError());
    }

    /**
     * Show error after set and show one time
     */
    public function testShowTBECountryChangedErrorShow()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->setTBECountryChanged();
        $this->assertTrue($oBasket->showTBECountryChangedError());
        $this->assertFalse($oBasket->showTBECountryChangedError());
    }

    /**
     * test for basket validation
     */
    public function testIsTBEValidValid()
    {
        $oChecker = $this->getMock('oeVATTBEOrderArticleChecker', array('isValid'), array(), '', false);
        $oChecker->expects($this->any())->method('isValid')->will($this->returnValue(true));

        $oBasket = $this->getMock('oeVATTBEOxBasket', array('_getOeVATTBEOrderArticleChecker'));
        $oBasket->expects($this->any())->method('_getOeVATTBEOrderArticleChecker')->will($this->returnValue($oChecker));

        $this->assertTrue($oBasket->isTBEValid());
    }

    /**
     * test for basket validation
     */
    public function testIsTBEValidNotValid()
    {
        $oChecker = $this->getMock('oeVATTBEOrderArticleChecker', array('isValid'), array(), '', false);
        $oChecker->expects($this->any())->method('isValid')->will($this->returnValue(false));

        $oBasket = $this->getMock('oeVATTBEOxBasket', array('_getOeVATTBEOrderArticleChecker'));
        $oBasket->expects($this->any())->method('_getOeVATTBEOrderArticleChecker')->will($this->returnValue($oChecker));

        $this->assertFalse($oBasket->isTBEValid());
    }
}
