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
