<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
        return (bool) $this->_getOeVATTBETbeCountryId();
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
            $sCacheKey .= '_'. $this->_getOeVATTBETbeCountryId();
            $aKeys[$sKey] = $sCacheKey;
        }

        return $aKeys;
    }

    /**
     * Returns users tbe country
     *
     * @return string
     */
    private function _getOeVATTBETbeCountryId()
    {
        $sCountryId = null;

        if ($this->_oUser) {
            $sCountryId = $this->_oUser->getOeVATTBETbeCountryId();
        }

        return $sCountryId;
    }
}
