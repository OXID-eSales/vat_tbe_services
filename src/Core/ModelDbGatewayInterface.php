<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Core;

interface ModelDbGatewayInterface
{
    /**
     * Abstract method for data saving (insert and update).
     *
     * @param array $aData model data
     */
    public function save($aData);

    /**
     * Abstract method for loading model data.
     *
     * @param string $sId model id
     */
    public function load($sId);

    /**
     * Abstract method for delete model data.
     *
     * @param string $sId model id
     */
    public function delete($sId);
}
