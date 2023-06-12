<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Controller\BasketController;
use OxidEsales\Eshop\Application\Controller\BasketController as EShopBasketController;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Shop\Basket;
use OxidEsales\EVatModule\Shop\Country;
use OxidEsales\Eshop\Application\Model\Country as EShopCountry;
use OxidEsales\EVatModule\Shop\User;
use OxidEsales\Eshop\Application\Model\User as EShopUser;
use OxidEsales\EVatModule\Traits\ServiceContainer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use OxidEsales\Eshop\Application\Model\Basket as EShopBasket;

/**
 * Testing extended Basket controller.
 */
class BasketControllerTest extends TestCase
{
    use ServiceContainer;

    /**
     * TBE Articles are in basket;
     * User is not logged in;
     * Marks message should be formed and domestic country should be set in message.
     */
    public function testGetOeVATTBEMarkMessageWhenUserIsNotLoggedIn()
    {
        Registry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');
        $this->getServiceFromContainer(ModuleSettings::class)->saveDomesticCountry('DE');

        $oBasket = $this->createPartialMock(Basket::class, ['hasOeTBEVATArticles']);
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        Registry::getSession()->setBasket($oBasket);

        /** @var BasketController|EShopBasketController $oBasketController */
        $oBasketController = oxNew(BasketController::class);
        $oBasketController->setUser(null);

        $sExpectedMessage = '** - ';
        $sExpectedMessage .= sprintf(Registry::getLang()->translateString('OEVATTBE_VAT_WILL_BE_CALCULATED_BY_USER_COUNTRY'), 'Deutschland');
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
        $this->getServiceFromContainer(ModuleSettings::class)->saveDomesticCountry('DE');

        /** @var Country|EShopCountry|MockObject $oCountry */
        $oCountry = $this->createPartialMock(Country::class, ['getOeVATTBEName']);
        $oCountry->expects($this->any())->method("getOeVATTBEName")->will($this->returnValue('Deutschland'));

        /** @var Basket|EShopBasket|MockObject $oBasket */
        $oBasket = $this->createPartialMock(Basket::class, ['hasOeTBEVATArticles', 'getOeVATTBECountry']);
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        Registry::getSession()->setBasket($oBasket);

        /** @var User|EShopUser $oUser */
        $oUser = oxNew(EShopUser::class);

        $oBasketController = oxNew(BasketController::class);
        $oBasketController->setUser($oUser);

        $sExpectedMessage = '** - ';
        $sExpectedMessage .= sprintf(Registry::getLang()->translateString('OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY'), 'Deutschland');
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
        $this->getServiceFromContainer(ModuleSettings::class)->saveDomesticCountry('DE');

        /** @var Basket|EShopBasket|MockObject $oBasket */
        $oBasket = $this->createPartialMock(Basket::class, ['hasOeTBEVATArticles', 'getOeVATTBECountry']);
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue(null));
        Registry::getSession()->setBasket($oBasket);

        /** @var User|EShopUser $oUser */
        $oUser = oxNew(EShopUser::class);

        $oBasketController = oxNew(BasketController::class);
        $oBasketController->setUser($oUser);

        $sExpectedMessage = '** - ';
        $sExpectedMessage .= sprintf(Registry::getLang()->translateString('OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY'), '');
        $this->assertEquals($sExpectedMessage, $oBasketController->getOeVATTBEMarkMessage());
    }

    /**
     * Provider for testOeVATTBEShowVATTBEMarkMessage.
     *
     * @return array
     */
    public function providerShowVATTBEMarkMessageWhenMessageShouldBeHidden(): array
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
        $this->getServiceFromContainer(ModuleSettings::class)->saveDomesticCountry($sDomesticCountryAbbr);
        Registry::getSession()->setVariable('TBECountryId', '8f241f11095d6ffa8.86593236'); // LT

        $oCountry = $this->createPartialMock(Country::class, ['appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue($blCountryAppliesTBEVAT));

        $oBasket = $this->createPartialMock(Basket::class, ['hasOeTBEVATArticles', 'isOeVATTBEValid', 'getOeVATTBECountry']);
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue($blHasTBEArticles));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue($blValidArticles));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        Registry::getSession()->setBasket($oBasket);

        $oBasketController = oxNew(BasketController::class);
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
        $this->getServiceFromContainer(ModuleSettings::class)->saveDomesticCountry('DE');
        Registry::getSession()->setVariable('TBECountryId', '8f241f11095d6ffa8.86593236'); // LT

        $oCountry = $this->createPartialMock(Country::class, ['appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(true));

        $oBasket = $this->createPartialMock(Basket::class, ['hasOeTBEVATArticles', 'isOeVATTBEValid', 'getOeVATTBECountry']);
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        Registry::getSession()->setBasket($oBasket);

        $oBasketController = oxNew(BasketController::class);
        $this->assertTrue($oBasketController->oeVATTBEShowVATTBEMarkMessage());
    }
}
