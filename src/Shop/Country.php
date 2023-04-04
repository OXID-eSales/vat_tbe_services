<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Shop;

/**
 * VAT TBE oxCountry class
 */
class Country extends Country_parent
{
    /**
     * Return if TBE VAT is used in this country
     *
     * @return bool
     */
    public function appliesOeTBEVATTbeVat()
    {
        return (bool) $this->getFieldData('oevattbe_appliestbevat');
    }

    /**
     * Return true if at least one TBE VAT group configured for this country.
     *
     * @return bool
     */
    public function isOEVATTBEAtLeastOneGroupConfigured()
    {
        return (bool) $this->getFieldData('oevattbe_istbevatconfigured');
    }

    /**
     * Return title
     *
     * @return string
     */
    public function getOeVATTBEName()
    {
        return $this->getFieldData('oxtitle');
    }
}
