<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Service;

use OxidEsales\Eshop\Core\UtilsObject;

/**
 * Store logic how to find articles with wrong TBE VAT.
 */
class CacheFactory
{
    public const CACHE_CLASS = '\OxidEsales\Eshop\Core\Cache\Generic\Cache';

    public function __construct(
        protected UtilsObject $utilsObject
    ) {
    }

    public function getCacheIfAvailable()
    {
        if (class_exists(self::CACHE_CLASS)) {
            return $this->utilsObject->oxNew(self::CACHE_CLASS);
        }

        return null;
    }
}
