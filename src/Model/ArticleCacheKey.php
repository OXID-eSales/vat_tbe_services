<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model;

use OxidEsales\Eshop\Application\Model\User;

/**
 * Has TBE TBE article logic.
 */
class ArticleCacheKey
{
    public function __construct(
        private User $user
    ) {
    }

    /**
     * Check if need to generate different cache keys.
     * We only need different cache keys if User country is known.
     *
     * @return bool
     */
    public function needToCalculateKeys()
    {
        return (bool) $this->getOeVATTBETbeCountryId();
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
            $sCacheKey .= '_'. $this->getOeVATTBETbeCountryId();
            $aKeys[$sKey] = $sCacheKey;
        }

        return $aKeys;
    }

    /**
     * Returns users tbe country
     *
     * @return string
     */
    private function getOeVATTBETbeCountryId()
    {
        return ($this->user) ? $this->user->getOeVATTBETbeCountryId() : null;
    }
}
