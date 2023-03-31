<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model\Evidence\Item;

/**
 * Class to get user country from billing address.
 */
class BillingCountryEvidence extends Evidence
{
    /**
     * Evidence name. Will be stored in Admin Order page if this evidence was used for selection.
     * Also used when selecting default evidence.
     */
    private string $id = 'billing_country';

    private string $countryId = '';

    /**
     * Returns evidence id.
     * Evidence id is shown in module configuration screen for admin to be able to active or deactivate this evidence.
     * It is also shown in order page if order has TBE articles and this evidence was used for country selection.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Calculates user country id and returns it.
     * For performance reasons country id is cached locally,
     * so that country would not be checked on every call.
     */
    public function getCountryId(): string
    {
        if (!$this->countryId && $this->getUser()) {
            $this->countryId = $this->getUser()->getFieldData('oxcountryid');
        }

        return $this->countryId;
    }
}
