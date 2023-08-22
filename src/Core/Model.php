<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Core;

/**
 * Abstract model class
 */
class Model
{
    /** @var string Model id. */
    protected $_sId = null;

    /** @var array Model data. */
    protected $_aData = null;

    /** @var bool Was object information found in database. */
    protected $_blIsLoaded = false;

    /**
     * Sets model id.
     *
     * @param string $sId Model id
     */
    public function setId($sId)
    {
        $this->_sId = $sId;
    }

    /**
     * Returns model id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->_sId;
    }

    /**
     * Set response data.
     *
     * @param array $aData model data
     */
    public function setData($aData)
    {
        $aData = array_change_key_case($aData, CASE_LOWER);
        $this->_aData = $aData;
    }

    /**
     * Return response data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->_aData;
    }

    /**
     * Method for model saving (insert and update data).
     *
     * @return int|false
     */
    public function save()
    {
        $mId = $this->getDbGateway()->save($this->getData());
        $this->setId($mId);

        return $mId;
    }

    /**
     * Delete model data from db.
     *
     * @param string $sId model id
     *
     * @return bool
     */
    public function delete($sId = null)
    {
        if (!is_null($sId)) {
            $this->setId($sId);
        }

        $blResult = $this->getDbGateway()->delete($this->getId());
        if ($blResult) {
            $this->setData(array());
        }

        return $blResult;
    }

    /**
     * Method for loading model, if loaded returns true.
     *
     * @param string $sId model id
     *
     * @return bool
     */
    public function load($sId = null)
    {
        if (!is_null($sId)) {
            $this->setId($sId);
        }

        $this->_blIsLoaded = false;
        $aData = $this->getDbGateway()->load($this->getId());
        if ($aData) {
            $this->setData($aData);
            $this->_blIsLoaded = true;
        }

        return $this->isLoaded();
    }

    /**
     * Returns whether object information found in database.
     *
     * @return bool
     */
    public function isLoaded()
    {
        return $this->_blIsLoaded;
    }

    /**
     * Return value from data by given key.
     *
     * @param string $sKey key of data value
     *
     * @return string
     */
    protected function getValue($sKey)
    {
        $aData = $this->getData();

        return $aData[$sKey];
    }

    /**
     * Return value from data by given key.
     *
     * @param string $sKey   key of data value
     * @param string $sValue data value
     */
    protected function setValue($sKey, $sValue)
    {
        $this->_aData[$sKey] = $sValue;
    }

    /**
     * Returns model database gateway.
     *
     * @return ModelDbGateway
     */
    protected function getDbGateway(): ModelDbGateway
    {
        return $this->dbGateway;
    }
}
