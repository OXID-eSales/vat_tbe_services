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

if (oxRegistry::getConfig()->getEdition() == 'EE') {
    /**
     * Mock connector class for CacheBackend invalidation testing.
     */
    class oxTestCacheConnector implements oxiCacheConnector
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
}
