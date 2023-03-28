<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */


/**
 * Class to get user country from billing address.
 */
class oeVATTBEBillingCountryEvidence extends oeVATTBEEvidence
{
    /**
     * Evidence name. Will be stored in Admin Order page if this evidence was used for selection.
     * Also used when selecting default evidence.
     *
     * @var string
     */
    private $_sId = 'billing_country';

    /** @var string Calculated user country. */
    private $_sCountry = null;

    /**
     * Returns evidence id.
     * Evidence id is shown in module configuration screen for admin to be able to active or deactivate this evidence.
     * It is also shown in order page if order has TBE articles and this evidence was used for country selection.
     *
     * @return string Evidence id.
     */
    public function getId()
    {
        return $this->_sId;
    }

    /**
     * Calculates user country id and returns it.
     * For performance reasons country id is cached locally,
     * so that country would not be checked on every call.
     *
     * @return string Country id.
     */
    public function getCountryId()
    {
        if (!$this->_sCountry) {
            $this->_sCountry = $this->_getBillingCountryId();
        }

        return $this->_sCountry;
    }

    /**
     * Returns Billing country id.
     *
     * @return string
     */
    private function _getBillingCountryId()
    {
        $oUser = $this->_getUser();

        return $oUser->oxuser__oxcountryid->value;
    }
}
