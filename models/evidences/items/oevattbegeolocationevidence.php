<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Class to get user country from geo location.
 * This class is not implemented and should be extended if this functionality is needed.
 * It can also be used as template class for other evidences.
 * Refer to user manual on how to register new evidences.
 */
class oeVATTBEGeoLocationEvidence extends oeVATTBEEvidence
{
    /**
     * Evidence id is shown in module configuration screen for admin to be able to active or deactivate this evidence.
     * It is also shown in order page if order has TBE articles and this evidence was used for country selection.
     *
     * @var string
     */
    private $_sId = 'geo_location';

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
     * For performance reasons country id should be cached locally,
     * so that country would not be checked on every call.
     *
     * @return string Country id.
     */
    public function getCountryId()
    {
        return '';
    }
}
