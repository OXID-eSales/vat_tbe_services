<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */


/**
 * Abstract class for all evidences.
 */
abstract class oeVATTBEEvidence
{
    /** @var oxUser User object to get data needed for finding user country. */
    private $_oUser = null;

    /**
     * Handles required dependencies.
     *
     * @param oxUser $oUser User object to get data needed for finding user country.
     */
    public function __construct($oUser)
    {
        $this->_oUser = $oUser;
    }

    /**
     * Returns evidence id.
     * Evidence id is shown in module configuration screen for admin to be able to active or deactivate this evidence.
     * It is also shown in order page if order has TBE articles and this evidence was used for country selection.
     *
     * @return string Evidence id.
     */
    abstract public function getId();

    /**
     * Calculates user country id and returns it.
     * For performance reasons country id should be cached locally,
     * so that country would not be checked on every call.
     *
     * @return string Country id.
     */
    abstract public function getCountryId();

    /**
     * Returns oxUser object.
     *
     * @return oxUser
     */
    protected function _getUser()
    {
        return $this->_oUser;
    }
}
