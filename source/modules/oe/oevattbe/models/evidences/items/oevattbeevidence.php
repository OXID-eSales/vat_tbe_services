<?php
/**
 * This file is part of OXID eSales VAT TBE module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales VAT TBE module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014T
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
