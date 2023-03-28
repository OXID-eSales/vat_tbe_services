<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * VAT TBE oxShop class
 */
class oeVATTBEOxShop extends oeVATTBEOxShop_parent
{
    /**
     * Returns country where shop is
     *
     * @return oxCountry|oeVATTBEoxCountry
     */
    public function getOeVATTBEDomesticCountry()
    {
        $oCountry = null;

        $sCountryISO2 = oxRegistry::getConfig()->getConfigParam('sOeVATTBEDomesticCountry');

        if ($sCountryISO2) {
            /** @var oxCountry|oeVATTBEoxCountry $oCountry */
            $oCountry = oxNew('oxCountry');

            if (!$oCountry->load($oCountry->getIdByCode($sCountryISO2))) {
                $oCountry = null;
            };

        }

        return $oCountry;
    }
}
