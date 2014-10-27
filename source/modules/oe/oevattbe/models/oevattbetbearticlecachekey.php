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

/**
 * Has TBE TBE article logic.
 */
class oeVATTBETBEArticleCacheKey
{
    /** @var oxUser */
    private $_oUser = null;

    /**
     * Create class with dependency.
     *
     * @param oxUser $oUser user to get country for cache key.
     */
    public function __construct($oUser)
    {
        $this->_oUser = $oUser;
    }

    /**
     * Check if need to generate different cache keys.
     * We only need different cache keys if User country is known.
     *
     * @return bool
     */
    public function needToCalculateKeys()
    {
        return (bool) $this->_getTbeCountryId();
    }

    /**
     * Update existing cache keys with TBE information.
     *
     * @param array $aKeys old cache keys to change by adding country.
     *
     * @return array
     */
    public function updateCacheKeys($aKeys)
    {
        foreach ($aKeys as $sKey => $sCacheKey) {
            $sCacheKey .= '_'. $this->_getTbeCountryId();
            $aKeys[$sKey] = $sCacheKey;
        }

        return $aKeys;
    }

    /**
     * Returns users tbe country
     *
     * @return string
     */
    private function _getTbeCountryId()
    {
        $sCountryId = null;

        if ($this->_oUser) {
            $sCountryId = $this->_oUser->getTbeCountryId();
        }

        return $sCountryId;
    }
}