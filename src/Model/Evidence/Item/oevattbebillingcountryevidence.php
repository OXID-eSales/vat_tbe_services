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

namespace OxidEsales\EVatModule\Model\Evidence\Item;

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
