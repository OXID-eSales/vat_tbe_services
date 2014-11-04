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
 * Class to get user country from geo location.
 */
class oeVATTBEExtendedCreditCardCountryEvidence extends oeVATTBEEvidence
{
    /**
     * Evidence id. It is shown in module configuration screen for admin to be able to active or deactivate this evidence.
     * It is also shown in order page if order has TBE articles and this evidence was used for user country selection.
     *
     * @var string
     */
    private $_sId = 'credit_card_country';

    /** @var string User country id. */
    private $_sCountryId = null;

    /**
     * Returns evidence id.
     *
     * @return string
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
     * @return string
     */
    public function getCountryId()
    {
        if (is_null($this->_sCountryId)) {
            $this->_sCountryId = '';
        }

        return $this->_sCountryId;
    }
}
