<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Shop;

use OxidEsales\Eshop\Application\Model\Country as EShopCountry;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * VAT TBE oxShop class
 */
class Shop extends Shop_parent
{
    use ServiceContainer;

    /**
     * Returns country where shop is
     *
     * @return EShopCountry|Country
     */
    public function getOeVATTBEDomesticCountry()
    {
        $oCountry = null;

        $sCountryISO2 = $this->getServiceFromContainer(ModuleSettings::class)->getDomesticCountry();;

        if ($sCountryISO2) {
            /** @var EShopCountry|Country $oCountry */
            $oCountry = oxNew(EShopCountry::class);

            if (!$oCountry->load($oCountry->getIdByCode($sCountryISO2))) {
                $oCountry = null;
            };

        }

        return $oCountry;
    }
}
