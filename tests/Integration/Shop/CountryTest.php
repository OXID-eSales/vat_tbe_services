<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Shop;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\EVatModule\Shop\Country;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

/**
 * Testing extended oxCountry class.
 */
class CountryTest extends IntegrationTestCase
{
    /**
     * Test for vat tbe getter
     */
    public function testAppliesOeTBEVATTbeVat()
    {
        $oCountry = oxNew(Country::class);
        $oCountry->assign(['oevattbe_appliestbevat' => 1]);
        $oCountry->save();
        $this->assertTrue($oCountry->appliesOeTBEVATTbeVat());

        $oCountry->assign(['oevattbe_appliestbevat' => 0]);
        $oCountry->save();
        $this->assertFalse($oCountry->appliesOeTBEVATTbeVat());
    }

    /**
     * Test if getter for data field works correct.
     */
    public function testIsOEVATTBEAtLeastOneGroupConfigured()
    {
        $oCountry = oxNew(Country::class);
        $oCountry->assign(['oevattbe_istbevatconfigured' => 1]);
        $oCountry->save();
        $this->assertTrue($oCountry->isOEVATTBEAtLeastOneGroupConfigured());

        $oCountry->assign(['oevattbe_istbevatconfigured' => 0]);
        $oCountry->save();
        $this->assertFalse($oCountry->isOEVATTBEAtLeastOneGroupConfigured());
    }

    /**
     * Test for vat tbe getter
     */
    public function testGetOeVATTBEName()
    {
        $oCountry = oxNew(Country::class);
        $oCountry->assign(['oxtitle' => 'LT']);
        $oCountry->save();
        $this->assertSame('LT', $oCountry->getOeVATTBEName());
    }
}
