<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Core;

use OxidEsales\Eshop\Core\DatabaseProvider;

/**
 * Model db gateway class.
 */
class ModelDbGateway
{
    /**
     * Returns data base resource.
     *
     * @return DatabaseProvider
     */
    protected function getDb()
    {
        return DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
    }
}
