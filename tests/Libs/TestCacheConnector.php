<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Libs;

/**
 * Mock connector class for CacheBackend invalidation testing.
 */
class TestCacheConnector implements \OxidEsales\Eshop\Application\Model\Contract\CacheConnectorInterface
{
    /** @var array */
    public $aCache = array();

    /**
     * Returns that cache is always active.
     *
     * @return bool
     */
    public static function isAvailable()
    {
        return true;
    }

    /**
     * Sets value to cache.
     *
     * @param string|array $mKey
     * @param mixed        $mValue
     * @param int          $iTTL
     */
    public function set($mKey, $mValue = null, $iTTL = null)
    {
        if (is_array($mKey)) {
            $this->aCache = array_merge($this->aCache, $mKey);
        } else {
            $this->aCache[$mKey] = $mValue;
        }
    }

    /**
     * Returns value in cache.
     *
     * @param array|string $mKey
     *
     * @return array
     */
    public function get($mKey)
    {
        if (is_array($mKey)) {
            return array_intersect_key($this->aCache, array_flip($mKey));
        } else {
            return $this->aCache[$mKey];
        }
    }

    /**
     * Removes item with given key from cache.
     *
     * @param array|string $mKey
     */
    public function invalidate($mKey)
    {
        if (is_array($mKey)) {
            $this->aCache = array_diff_key($this->aCache, array_flip($mKey));
        } else {
            $this->aCache[$mKey] = null;
        }
    }

    /**
     * Removes all items from cache.
     */
    public function flush()
    {
        $this->aCache = array();
    }
}
