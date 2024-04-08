<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Component;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Component\BasketComponent;
use OxidEsales\EVatModule\Shop\Basket;
use OxidEsales\EVatModule\Shop\Country;
use OxidEsales\EVatModule\Shop\User;
use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxUser class.
 */
class BasketComponentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Registry::getSession()->setUser(null);
    }

    /**
     * Render test
     */
    public function testRenderBasketWithoutTbeCountry()
    {
        $oCountry = $this->createPartialMock(Country::class, ["appliesOeTBEVATTbeVat"]);
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(true));

        $oUser = $this->getMockBuilder(User::class)
            ->onlyMethods(array("getOeVATTBETbeCountryId"))
            ->getMock();
        $oUser->expects($this->any())->method('getOeVATTBETbeCountryId')->will($this->returnValue('DE'));

        $oBasket = $this->createPartialMock(Basket::class, ['hasOeTBEVATArticles', 'getOeVATTBECountry', 'findDelivCountry']);
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));
        $oBasket->expects($this->any())->method('getOeVATTBECountry')->will($this->returnValue($oCountry));
        $oBasket->expects($this->any())->method('findDelivCountry')->willReturn('DE');

        Registry::getSession()->setBasket($oBasket);

        $oCmp_Basket = oxNew(BasketComponent::class);
        $oCmp_Basket->setUser($oUser);

        $oBasket = $oCmp_Basket->render();

        $this->assertSame('DE', $oBasket->getOeVATTBETbeCountryId());
        $this->assertTrue($oBasket->showOeVATTBECountryChangedError());
    }

    /**
     * Render test
     */
    public function testRenderBasketWithTbeCountry()
    {
        $oCountry = $this->createPartialMock(Country::class, ["appliesOeTBEVATTbeVat"]);
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(true));

        $oUser = $this->createPartialMock(User::class, ['getOeVATTBETbeCountryId']);
        $oUser->expects($this->any())->method('getOeVATTBETbeCountryId')->will($this->returnValue('DE'));

        $oBasket = $this->createPartialMock(Basket::class, ['hasOeTBEVATArticles', 'getOeVATTBECountry', 'findDelivCountry']);
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));
        $oBasket->expects($this->any())->method('getOeVATTBECountry')->will($this->returnValue($oCountry));
        $oBasket->expects($this->any())->method('findDelivCountry')->willReturn('LT');
        $oBasket->setOeVATTBECountryId('LT');

        Registry::getSession()->setBasket($oBasket);

        $oCmp_Basket = oxNew(BasketComponent::class);
        $oCmp_Basket->setUser($oUser);

        $oBasket = $oCmp_Basket->render();

        $this->assertSame('DE', $oBasket->getOeVATTBETbeCountryId());
        $this->assertTrue($oBasket->showOeVATTBECountryChangedError());
    }

    /**
     * Render test
     */
    public function testRenderBasketWithNotTbeCountry()
    {
        $oCountry = $this->createPartialMock(Country::class, ["appliesOeTBEVATTbeVat"]);
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(false));

        $oUser = $this->createPartialMock(User::class, ['getOeVATTBETbeCountryId']);
        $oUser->expects($this->any())->method('getOeVATTBETbeCountryId')->will($this->returnValue('DE'));

        $oBasket = $this->createPartialMock(Basket::class, ['hasOeTBEVATArticles', 'getOeVATTBECountry', 'findDelivCountry']);
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));
        $oBasket->expects($this->any())->method('getOeVATTBECountry')->will($this->returnValue($oCountry));
        $oBasket->expects($this->any())->method('findDelivCountry')->willReturn('LT');
        $oBasket->setOeVATTBECountryId('LT');

        Registry::getSession()->setBasket($oBasket);

        $oCmp_Basket = oxNew(BasketComponent::class);
        $oCmp_Basket->setUser($oUser);

        $oBasket = $oCmp_Basket->render();

        $this->assertSame('DE', $oBasket->getOeVATTBETbeCountryId());
        $this->assertFalse($oBasket->showOeVATTBECountryChangedError());
    }

    /**
     * Render test
     */
    public function testRenderBasketWithTbeCountryNoTBEArticles()
    {
        $oUser = $this->createPartialMock(User::class, ['getOeVATTBETbeCountryId']);
        $oUser->expects($this->any())->method('getOeVATTBETbeCountryId')->will($this->returnValue('DE'));

        $oBasket = $this->createPartialMock(Basket::class, ['hasOeTBEVATArticles', 'findDelivCountry']);
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(false));
        $oBasket->expects($this->any())->method('findDelivCountry')->willReturn('LT');
        $oBasket->setOeVATTBECountryId('LT');

        Registry::getSession()->setBasket($oBasket);

        $oCmp_Basket = oxNew(BasketComponent::class);
        $oCmp_Basket->setUser($oUser);

        $oBasket = $oCmp_Basket->render();

        $this->assertSame('DE', $oBasket->getOeVATTBETbeCountryId());
        $this->assertFalse($oBasket->showOeVATTBECountryChangedError());
    }

    /**
     * Render test
     */
    public function testRenderBasketWithTbeSameCountry()
    {
        $oUser = $this->createPartialMock(User::class, ['getOeVATTBETbeCountryId']);
        $oUser->expects($this->any())->method('getOeVATTBETbeCountryId')->will($this->returnValue('DE'));

        $oBasket = $this->createPartialMock(Basket::class, ['hasOeTBEVATArticles', 'findDelivCountry']);
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));
        $oBasket->expects($this->any())->method('findDelivCountry')->willReturn('DE');
        $oBasket->setOeVATTBECountryId('DE');

        Registry::getSession()->setBasket($oBasket);

        $oCmp_Basket = oxNew(BasketComponent::class);
        $oCmp_Basket->setUser($oUser);

        $oBasket = $oCmp_Basket->render();

        $this->assertSame('DE', $oBasket->getOeVATTBETbeCountryId());
        $this->assertFalse($oBasket->showOeVATTBECountryChangedError());
    }
}
