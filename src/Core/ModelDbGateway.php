<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Core;

use \oxDb;
use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;

/**
 * Abstract model db gateway class.
 */
abstract class ModelDbGateway
{
    /**
     * Returns data base resource.
     *
     * @return DatabaseInterface
     */
    protected function getDb()
    {
        return oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
    }

    /**
     * Abstract method for data saving (insert and update).
     *
     * @param array $aData model data
     */
    abstract public function save($aData);

    /**
     * Abstract method for loading model data.
     *
     * @param string $sId model id
     */
    abstract public function load($sId);

    /**
     * Abstract method for delete model data.
     *
     * @param string $sId model id
     */
    abstract public function delete($sId);
}
