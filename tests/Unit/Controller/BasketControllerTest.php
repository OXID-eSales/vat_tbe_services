<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Controller;

use OxidEsales\Eshop\Core\Registry;
use PHPUnit\Framework\TestCase;

/**
 * Testing extended Basket controller.
 *
 * @covers BasketController
 */
class BasketControllerTest extends TestCase
{
    /**
     * TBE Articles are in basket;
     * User is not logged in;
     * Marks message should be formed and domestic country should be set in message.
     */
    public function testGetOeVATTBEMarkMessageWhenUserIsNotLoggedIn()
    {
        Registry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        Registry::getSession()->setBasket($oBasket);

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
        Registry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');

        /** @var oxCountry|oeVATTBEOxCategory|PHPUnit_Framework_MockObject_MockObject $oCountry */
        $oCountry = $this->getMock("oeVATTBEOxCategory", array('getOeVATTBEName'));
        $oCountry->expects($this->any())->method("getOeVATTBEName")->will($this->returnValue('Deutschland'));

        /** @var oxBasket|oeVATTBEOxBasket|PHPUnit_Framework_MockObject_MockObject $oBasket */
        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'getOeVATTBECountry'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        Registry::getSession()->setBasket($oBasket);

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
        Registry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');

        /** @var oxBasket|oeVATTBEOxBasket|PHPUnit_Framework_MockObject_MockObject $oBasket */
        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'getOeVATTBECountry'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue(null));
        Registry::getSession()->setBasket($oBasket);

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
        Registry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', $sDomesticCountryAbbr);
        Registry::getSession()->setVariable('TBECountryId', '8f241f11095d6ffa8.86593236'); // LT

        $oCountry = $this->getMock("oeVATTBEOxCountry", array('appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue($blCountryAppliesTBEVAT));

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'isOeVATTBEValid', 'getOeVATTBECountry'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue($blHasTBEArticles));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue($blValidArticles));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        Registry::getSession()->setBasket($oBasket);

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
        Registry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');
        Registry::getSession()->setVariable('TBECountryId', '8f241f11095d6ffa8.86593236'); // LT

        $oCountry = $this->getMock("oeVATTBEOxCountry", array('appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(true));

        $oBasket = $this->getMock("oeVATTBEOxBasket", array('hasOeTBEVATArticles', 'isOeVATTBEValid', 'getOeVATTBECountry'));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        Registry::getSession()->setBasket($oBasket);

        $oBasketController = oxNew('oeVATTBEBasket');
        $this->assertTrue($oBasketController->oeVATTBEShowVATTBEMarkMessage());
    }
}
