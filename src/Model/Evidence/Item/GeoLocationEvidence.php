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
 * Class to get user country from geo location.
 * This class is not implemented and should be extended if this functionality is needed.
 * It can also be used as template class for other evidences.
 * Refer to user manual on how to register new evidences.
 */
class GeoLocationEvidence extends Evidence
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
