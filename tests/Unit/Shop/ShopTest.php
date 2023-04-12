<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Shop;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Shop\Country;
use OxidEsales\EVatModule\Shop\Shop;
use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxShop class.
 *
 * @covers Shop
 */
class ShopTest extends TestCase
{
    /**
     * Test country not set
     */
    public function testGetDomesticCountryNotSet()
    {
        Registry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', null);

        /** @var Shop $oShop */
        $oShop = oxNew(Shop::class);
        $this->assertNull($oShop->getOeVATTBEDomesticCountry());
    }

    /**
     * Test country set but not exist
     */
    public function testGetDomesticCountryWrong()
    {
        Registry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'blabla');

        /** @var Shop $oShop */
        $oShop = oxNew(Shop::class);
        $this->assertNull($oShop->getOeVATTBEDomesticCountry());
    }

    /**
     * Test country set
     */
    public function testGetDomesticCountry()
    {
        Registry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');

        /** @var Shop $oShop */
        $oShop = oxNew(Shop::class);
        $this->assertTrue($oShop->getOeVATTBEDomesticCountry() instanceof Country);
        $this->assertSame('Deutschland', $oShop->getOeVATTBEDomesticCountry()->getOeVATTBEName());
    }
}
