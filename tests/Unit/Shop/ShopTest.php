<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\VisualCmsModule\Tests\Unit\Shop;

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
        oxRegistry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', null);

        /** @var oeVATTBEOxShop $oShop */
        $oShop = oxNew('oeVATTBEOxShop');
        $this->assertNull($oShop->getOeVATTBEDomesticCountry());
    }

    /**
     * Test country set but not exist
     */
    public function testGetDomesticCountryWrong()
    {
        oxRegistry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'blabla');

        /** @var oeVATTBEOxShop $oShop */
        $oShop = oxNew('oeVATTBEOxShop');
        $this->assertNull($oShop->getOeVATTBEDomesticCountry());
    }

    /**
     * Test country set
     */
    public function testGetDomesticCountry()
    {
        oxRegistry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');

        /** @var oeVATTBEOxShop $oShop */
        $oShop = oxNew('oeVATTBEOxShop');
        $this->assertTrue($oShop->getOeVATTBEDomesticCountry() instanceof oeVATTBEOxCountry);
        $this->assertSame('Deutschland', $oShop->getOeVATTBEDomesticCountry()->getOeVATTBEName());
    }
}
