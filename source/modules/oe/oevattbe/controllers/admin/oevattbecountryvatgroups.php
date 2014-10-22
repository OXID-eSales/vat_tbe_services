<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

/**
 * Display VAT groups for particular country.
 */
class oeVATTBECountryVatGroups extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oxOrder object,
     * passes it's data to Smarty engine and returns
     * name of template file "order_paypal.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        return "oevattbecountryvatgroups.tpl";
    }
}
