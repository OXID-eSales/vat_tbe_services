<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Shop;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Shop\Country;
use OxidEsales\EVatModule\Shop\Shop;
use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxShop class.
 */
class ShopTest extends TestCase
{
    /**
     * Test country not set
     */
    public function testGetDomesticCountryNotSet()
    {
        ContainerFacade::get(ModuleSettings::class)->saveDomesticCountry('');

        /** @var Shop $oShop */
        $oShop = oxNew(Shop::class);
        $this->assertNull($oShop->getOeVATTBEDomesticCountry());
    }

    /**
     * Test country set but not exist
     */
    public function testGetDomesticCountryWrong()
    {
        ContainerFacade::get(ModuleSettings::class)->saveDomesticCountry('blabla');

        /** @var Shop $oShop */
        $oShop = oxNew(Shop::class);
        $this->assertNull($oShop->getOeVATTBEDomesticCountry());
    }

    /**
     * Test country set
     */
    public function testGetDomesticCountry()
    {
        ContainerFacade::get(ModuleSettings::class)->saveDomesticCountry('DE');

        /** @var Shop $oShop */
        $oShop = oxNew(Shop::class);
        $this->assertTrue($oShop->getOeVATTBEDomesticCountry() instanceof Country);
        $this->assertSame('Deutschland', $oShop->getOeVATTBEDomesticCountry()->getOeVATTBEName());
    }
}
