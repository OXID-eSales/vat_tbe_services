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
 * Testing oeVATTBEBasketVATValidator class.
 *
 * @covers oeVATTBEBasketVATValidator
 */
class Unit_oeVatTbe_models_oeVATTBEBasketVATValidatorTest extends OxidTestCase
{

    /**
     * data provider for test testShowVATTBEMark
     *
     * @return array
     */
    public function providerShowVATTBEMark()
    {
        return array(
            array(true, true, true, true),
            array(false, true, true, true),
            array(false, true, false, true),
            array(true, false, true, false),
            array(false, false, true, false),
            array(true, true, false, false),
            array(true, false, false, false),
            array(false, false, false, false),
        );
    }

    /**
     * Basket Vat Validator test for showVATTBEMark method.
     *
     * @param bool $blIsUserLoggedIn      User logged in or not
     * @param bool $blIsArticleTbeService Article tbe or not
     * @param bool $blIsCountryConfigured Configured country or not
     * @param bool $blResult              Expected result
     *
     * @dataProvider providerShowVATTBEMark
     */
    public function testShowVATTBEMark($blIsUserLoggedIn, $blIsArticleTbeService, $blIsCountryConfigured, $blResult)
    {
        /** @var oxCountry|oeVATTBEOxCountry|PHPUnit_Framework_MockObject_MockObject $oBasketItem */
        $oCountry = $this->getMock("oeVATTBEoxCountry", array('appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue($blIsCountryConfigured));

        /** @var oxBasket|oeVATTBEOxBasket|PHPUnit_Framework_MockObject_MockObject $oBasketItem */
        $oBasket = $this->getMock("oeVATTBEOxBasket", array('getOeVATTBECountry', 'isOeVATTBEValid'));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue(true));

        /** @var oxUser|null $oUser */
        $oUser = ($blIsUserLoggedIn) ? oxNew('oxUser') : null;

        /** @var oeVATTBETBEUser|PHPUnit_Framework_MockObject_MockObject $oBasketItem */
        $oTBEUserCountry = $this->getMock("oeVATTBETBEUser", array('isUserFromDomesticCountry'), array(), '', false);
        $oTBEUserCountry->expects($this->any())->method("isUserFromDomesticCountry")->will($this->returnValue(false));

        /** @var oxArticle|oeVATTBEOxArticle|PHPUnit_Framework_MockObject_MockObject $oBasketItem */
        $oArticle = $this->getMock("oeVATTBEOxArticle", array('isOeVATTBETBEService'));
        $oArticle->expects($this->any())->method("isOeVATTBETBEService")->will($this->returnValue($blIsArticleTbeService));

        /** @var oxBasketItem|PHPUnit_Framework_MockObject_MockObject $oBasketItem */
        $oBasketItem = $this->getMock("oxBasketItem", array('getVatPercent', 'getArticle'));
        $oBasketItem->expects($this->any())->method("getVatPercent")->will($this->returnValue(10));
        $oBasketItem->expects($this->any())->method("getArticle")->will($this->returnValue($oArticle));

        /** @var oeVATTBEBasketVATValidator $oValidator */
        $oValidator = oxNew('oeVATTBEBasketVATValidator', $oBasket, $oUser, $oTBEUserCountry);

        $this->assertSame($blResult, $oValidator->showVATTBEMark($oBasketItem));
    }

    /**
     * data provider for test testIsTBEArticleValid
     *
     * @return array
     */
    public function providerIsTBEArticleValid()
    {
        return array(
            array(false, false),
            array(true, true),
        );
    }

    /**
     * Basket Vat Validator test for isTBEArticleValid method.
     *
     * @param bool   $blIsArticleValid Article is valid / invalid
     * @param string $sExpectValue     Expected value
     *
     * @dataProvider providerIsTBEArticleValid
     */
    public function testIsTBEArticleValid($blIsArticleValid, $sExpectValue)
    {
        $oCountry = $this->getMock("oeVATTBEoxCountry", array('appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(true));

        $oArticle = $this->getMock("oeVATTBEoxArticle", array('isOeVATTBETBEService', 'getId'));
        $oArticle->expects($this->any())->method("isOeVATTBETBEService")->will($this->returnValue(true));
        $ArticleId = ($blIsArticleValid) ? 'id' : 'id1';
        $oArticle->expects($this->any())->method("getId")->will($this->returnValue($ArticleId));

        $oBasketItem = $this->getMock("oxBasketItem", array('getVatPercent', 'getArticle'));
        $oBasketItem->expects($this->any())->method("getVatPercent")->will($this->returnValue(10));
        $oBasketItem->expects($this->any())->method("getArticle")->will($this->returnValue($oArticle));

        $oBasket = $this->getMock("oeVATTBEoxBasket", array('getOeVATTBECountry', 'isOeVATTBEValid', 'getOeVATTBEInValidArticles'));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue(false));
        $aInValidArticles = array('id1'=>'article1', 'id2'=>'article2');
        $oBasket->expects($this->any())->method("getOeVATTBEInValidArticles")->will($this->returnValue($aInValidArticles));

        /** @var oeVATTBETBEUser|PHPUnit_Framework_MockObject_MockObject $oBasketItem */
        $oTBEUserCountry = $this->getMock("oeVATTBETBEUser", array('isUserFromDomesticCountry'), array(), '', false);
        $oTBEUserCountry->expects($this->any())->method("isUserFromDomesticCountry")->will($this->returnValue(false));

        /** @var oxUser $oUser */
        $oUser = oxNew('oxUser');

        /** @var oeVATTBEBasketVATValidator $oValidator */
        $oValidator = oxNew('oeVATTBEBasketVATValidator', $oBasket, $oUser, $oTBEUserCountry);

        $this->assertSame($sExpectValue, $oValidator->isArticleValid($oBasketItem));
    }

    /**
     * When user is from domestic country, all articles should always be valid.
     */
    public function testIsTBEArticleValidWhenUserFromDomesticCountry()
    {
        $oCountry = $this->getMock("oeVATTBEoxCountry", array('appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(true));

        $oArticle = $this->getMock("oeVATTBEoxArticle", array('isOeVATTBETBEService', 'getId'));
        $oArticle->expects($this->any())->method("isOeVATTBETBEService")->will($this->returnValue(true));
        $oArticle->expects($this->any())->method("getId")->will($this->returnValue('invalid_article_id'));

        $oBasketItem = $this->getMock("oxBasketItem", array('getArticle'));
        $oBasketItem->expects($this->any())->method("getArticle")->will($this->returnValue($oArticle));

        $aInValidArticles = array('invalid_article_id'=>'article1');
        $oBasket = $this->getMock("oeVATTBEoxBasket", array('getOeVATTBECountry', 'isOeVATTBEValid', 'getOeVATTBEInValidArticles'));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue(false));
        $oBasket->expects($this->any())->method("getOeVATTBEInValidArticles")->will($this->returnValue($aInValidArticles));

        /** @var oeVATTBETBEUser|PHPUnit_Framework_MockObject_MockObject $oBasketItem */
        $oTBEUserCountry = $this->getMock("oeVATTBETBEUser", array('isUserFromDomesticCountry'), array(), '', false);
        $oTBEUserCountry->expects($this->any())->method("isUserFromDomesticCountry")->will($this->returnValue(true));

        /** @var oxUser $oUser */
        $oUser = oxNew('oxUser');

        /** @var oeVATTBEBasketVATValidator $oValidator */
        $oValidator = oxNew('oeVATTBEBasketVATValidator', $oBasket, $oUser, $oTBEUserCountry);

        $this->assertTrue($oValidator->isArticleValid($oBasketItem));
    }

    /**
     * When user is from domestic country, no marks should be added to any articles.
     */
    public function testShowVATTBEMarkWhenUserFromDomesticCountry()
    {
        /** @var oxCountry|oeVATTBEOxCountry|PHPUnit_Framework_MockObject_MockObject $oBasketItem */
        $oCountry = $this->getMock("oeVATTBEoxCountry", array('appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(true));

        /** @var oxArticle|oeVATTBEOxArticle|PHPUnit_Framework_MockObject_MockObject $oBasketItem */
        $oArticle = $this->getMock("oeVATTBEOxArticle", array('isOeVATTBETBEService'));
        $oArticle->expects($this->any())->method("isOeVATTBETBEService")->will($this->returnValue(true));

        /** @var oxBasketItem|PHPUnit_Framework_MockObject_MockObject $oBasketItem */
        $oBasketItem = $this->getMock("oxBasketItem", array('getArticle'));
        $oBasketItem->expects($this->any())->method("getArticle")->will($this->returnValue($oArticle));

        /** @var oxBasket|oeVATTBEOxBasket|PHPUnit_Framework_MockObject_MockObject $oBasketItem */
        $oBasket = $this->getMock("oeVATTBEOxBasket", array('getOeVATTBECountry', 'isOeVATTBEValid'));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue(true));

        /** @var oeVATTBETBEUser|PHPUnit_Framework_MockObject_MockObject $oBasketItem */
        $oTBEUserCountry = $this->getMock("oeVATTBETBEUser", array('isUserFromDomesticCountry'), array(), '', false);
        $oTBEUserCountry->expects($this->any())->method("isUserFromDomesticCountry")->will($this->returnValue(true));

        /** @var oeVATTBEBasketVATValidator $oValidator */
        $oValidator = oxNew('oeVATTBEBasketVATValidator', $oBasket, null, $oTBEUserCountry);

        $this->assertFalse($oValidator->showVATTBEMark($oBasketItem));
    }
}
