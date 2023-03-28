<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * VAT TBE oxCountry class
 */
class oeVATTBEOxCountry extends oeVATTBEOxCountry_parent
{
    /**
     * Return if TBE VAT is used in this country
     *
     * @return bool
     */
    public function appliesOeTBEVATTbeVat()
    {
        return (bool) $this->oxcountry__oevattbe_appliestbevat->value;
    }

    /**
     * Return true if at least one TBE VAT group configured for this country.
     *
     * @return bool
     */
    public function isOEVATTBEAtLeastOneGroupConfigured()
    {
        return (bool) $this->oxcountry__oevattbe_istbevatconfigured->value;
    }

    /**
     * Return title
     *
     * @return string
     */
    public function getOeVATTBEName()
    {
        return $this->oxcountry__oxtitle->value;
    }
}
