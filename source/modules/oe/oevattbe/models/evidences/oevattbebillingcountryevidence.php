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
    private $_sName = 'billing_country';

    /** @var string Calculated user country. */
    private $_sCountry = null;

    /**
     * Returns the name of evidence.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_sName;
    }

    /**
     * Gets user country id and returns it.
     * Has local cache, so does not recheck twice.
     *
     * @return string
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
