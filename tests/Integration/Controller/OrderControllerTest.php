<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EVatModule\Controller\OrderController;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Shop\Basket;
use OxidEsales\EVatModule\Shop\Country;
use PHPUnit\Framework\TestCase;

/**
 * Testing extended Order controller.
 */
class OrderControllerTest extends TestCase
{
    /**
     * Provider for testOeVATTBEShowVATTBEMarkMessage.
     *
     * @return array
     */
    public static function providerShowVATTBEMarkMessageWhenMessageShouldBeHidden(): array
    {
        return [
            [true, true, true, true],
            [false, true, true, false],
            [false, false, true, true],
            [false, false, false, false],
        ];
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
        ContainerFacade::get(ModuleSettings::class)->saveDomesticCountry($sDomesticCountryAbbr);
        Registry::getSession()->setVariable('TBECountryId', '8f241f11095d6ffa8.86593236'); // LT

        $oCountry = $this->createPartialMock(Country::class, ['appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue($blCountryAppliesTBEVAT));

        $oBasket = $this->createPartialMock(Basket::class, ['hasOeTBEVATArticles', 'isOeVATTBEValid', 'getOeVATTBECountry']);
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue($blHasTBEArticles));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue($blValidArticles));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        Registry::getSession()->setBasket($oBasket);

        $oOrderController = oxNew(OrderController::class);
        $this->assertFalse($oOrderController->oeVATTBEShowVATTBEMarkMessage());
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
        ContainerFacade::get(ModuleSettings::class)->saveDomesticCountry('DE');
        Registry::getSession()->setVariable('TBECountryId', '8f241f11095d6ffa8.86593236'); // LT

        $oCountry = $this->createPartialMock(Country::class, ['appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(true));

        $oBasket = $this->createPartialMock(Basket::class, ['hasOeTBEVATArticles', 'isOeVATTBEValid', 'getOeVATTBECountry']);
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        Registry::getSession()->setBasket($oBasket);

        $oOrderController = oxNew(OrderController::class);
        $this->assertTrue($oOrderController->oeVATTBEShowVATTBEMarkMessage());
    }

    /**
     * Testing message formation for showing it in order step.
     */
    public function testGetMarkMessageHasTBEArticleInBasketValidCountryTBE()
    {
        $oCountry = $this->createPartialMock(Country::class, ["appliesOeTBEVATTbeVat", 'getOeVATTBEName']);
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(true));
        $oCountry->expects($this->any())->method("getOeVATTBEName")->will($this->returnValue('Deutschland'));

        $oBasket = $this->createPartialMock(Basket::class, ['hasOeTBEVATArticles', 'isOeVATTBEValid', 'getOeVATTBECountry']);
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));

        Registry::getSession()->setBasket($oBasket);

        $oOrderController = oxNew(OrderController::class);

        $sExpectedMessage = '** - ';
        $sExpectedMessage .= sprintf(Registry::getLang()->translateString('OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY'), 'Deutschland');
        $this->assertEquals($sExpectedMessage, $oOrderController->getOeVATTBEMarkMessage());
    }
}
