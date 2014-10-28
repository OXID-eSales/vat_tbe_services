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
 * Testing TBEUser class.
 *
 * @covers oeVATTBETBEUser
 */
class Unit_oeVatTbe_models_oeVATTBEBasketItemVatFormatterTest extends OxidTestCase
{

    /**
     * data provider for test testFormatVatValidBasket
     *
     * @return array
     */
    public function providerForFormatVatValidBasket()
    {
        return array(
            array(true, true, true,'10% **'),
            array(false, true, true, '10% **'),
            array(true, false, true,'10%'),
            array(false, false, true,'10%'),
            array(true, true, false,'10%'),
            array(false, true, false, '10% **'),
            array(true, false, false,'10%'),
            array(false, false, false,'10%'),
        );
    }

    /**
     * Vat Formatter test when basket has all valid items
     *
     * @param bool   $blIsUserLoggedIn      user logged in or not
     * @param bool   $blIsArticleTbeService article tbe or not
     * @param bool   $blIsCountryConfigured configured country or not
     * @param string $sExpectValue          expected value
     *
     * @dataProvider providerForFormatVatValidBasket
     */
    public function testFormatVatValidBasket($blIsUserLoggedIn, $blIsArticleTbeService, $blIsCountryConfigured, $sExpectValue)
    {
        $oMarkGenerator = $this->getMock("oeVATTBEBasketItemVATFormatter", array('getMark'), array(), '', false);
        $oMarkGenerator->expects($this->any())->method("getMark")->will($this->returnValue('**'));

        $oCountry = $this->getMock("oeVATTBEoxCountry", array('appliesTBEVAT'));
        $oCountry->expects($this->any())->method("appliesTBEVAT")->will($this->returnValue($blIsCountryConfigured));

        $oArticle = $this->getMock("oeVATTBEoxArticle", array('oeVATTBEisTBEService'));
        $oArticle->expects($this->any())->method("oeVATTBEisTBEService")->will($this->returnValue($blIsArticleTbeService));

        $oBasketItem = $this->getMock("oxBasketItem", array('getVatPercent', 'getArticle'));
        $oBasketItem->expects($this->any())->method("getVatPercent")->will($this->returnValue(10));
        $oBasketItem->expects($this->any())->method("getArticle")->will($this->returnValue($oArticle));

        $oBasket = $this->getMock("oeVATTBEoxBasket", array('getUser', 'getTBECountry', 'isTBEValid'));
        $oUser = ($blIsUserLoggedIn) ? oxNew('oxUser') : null;
        $oBasket->expects($this->any())->method("getUser")->will($this->returnValue($oUser));
        $oBasket->expects($this->any())->method("getTBECountry")->will($this->returnValue($oCountry));
        $oBasket->expects($this->any())->method("isTBEValid")->will($this->returnValue(true));

        $oFormatter = oxNew('oeVATTBEBasketItemVATFormatter', $oBasket, $oMarkGenerator);

        $this->assertSame($sExpectValue, $oFormatter->formatVAT($oBasketItem));
    }

    /**
     * data provider for test testFormatVatInValidBasket
     *
     * @return array
     */
    public function providerForFormatVatInValidBasket()
    {
        return array(
            array(false, '10% **'),
            array(true, '-'),
        );
    }

    /**
     * Vat Formatter test when basket has all valid items
     *
     * @param bool   $blIsInvalidArticle article is valid / invalid
     * @param string $sExpectValue       expected value
     *
     * @dataProvider providerForFormatVatInValidBasket
     */
    public function testFormatVatInValidBasket($blIsInvalidArticle, $sExpectValue)
    {
        $oMarkGenerator = $this->getMock("oeVATTBEBasketItemVATFormatter", array('getMark'), array(), '', false);
        $oMarkGenerator->expects($this->any())->method("getMark")->will($this->returnValue('**'));

        $oCountry = $this->getMock("oeVATTBEoxCountry", array('appliesTBEVAT'));
        $oCountry->expects($this->any())->method("appliesTBEVAT")->will($this->returnValue(true));

        $oArticle = $this->getMock("oeVATTBEoxArticle", array('oeVATTBEisTBEService', 'getId'));
        $oArticle->expects($this->any())->method("oeVATTBEisTBEService")->will($this->returnValue(true));
        $ArticleId = ($blIsInvalidArticle) ? 'id1' : 'id';
        $oArticle->expects($this->any())->method("getId")->will($this->returnValue($ArticleId));


        $oBasketItem = $this->getMock("oxBasketItem", array('getVatPercent', 'getArticle'));
        $oBasketItem->expects($this->any())->method("getVatPercent")->will($this->returnValue(10));
        $oBasketItem->expects($this->any())->method("getArticle")->will($this->returnValue($oArticle));

        $oBasket = $this->getMock("oeVATTBEoxBasket", array('getUser', 'getTBECountry', 'isTBEValid', 'getTBEInValidArticles'));
        $oUser = oxNew('oxUser');
        $oBasket->expects($this->any())->method("getUser")->will($this->returnValue($oUser));
        $oBasket->expects($this->any())->method("getTBECountry")->will($this->returnValue($oCountry));
        $oBasket->expects($this->any())->method("isTBEValid")->will($this->returnValue(false));
        $aInValidArticles = array('id1'=>'article1', 'id2'=>'article2');
        $oBasket->expects($this->any())->method("getTBEInValidArticles")->will($this->returnValue($aInValidArticles));

        $oFormatter = oxNew('oeVATTBEBasketItemVATFormatter', $oBasket, $oMarkGenerator);

        $this->assertSame($sExpectValue, $oFormatter->formatVAT($oBasketItem));
    }
}
