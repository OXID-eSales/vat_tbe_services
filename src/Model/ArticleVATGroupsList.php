<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model;

use OxidEsales\EVatModule\Core\Model;
use OxidEsales\EVatModule\Model\DbGateway\ArticleVATGroupsDbGateway;

/**
 * VAT Groups handling class
 */
class ArticleVATGroupsList extends Model
{
    /** @var array Model data. */
    protected $_aData = array();

    public function __construct(
        protected ArticleVATGroupsDbGateway $dbGateway
    )
    {
    }

    /**
     * Method for model saving (insert and update data).
     *
     * @return int|false
     */
    public function save()
    {
        $aData = $this->getData();
        $aDbData = array();
        foreach ($aData as $sCountryId => $sGroupId) {
            if ($sGroupId) {
                $aDbData[] = array(
                    'OEVATTBE_ARTICLEID' => $this->getId(),
                    'OEVATTBE_COUNTRYID' => $sCountryId,
                    'OEVATTBE_VATGROUPID' => $sGroupId
                );
            }
        }

        $aData = array(
            'articleid' => $this->getId(),
            'relations' => $aDbData
        );
        $this->getDbGateway()->save($aData);

        return $this->getId();
    }

    /**
     * Method for loading article VAT group list. If loaded - returns true.
     *
     * @param string $sId Article id.
     *
     * @return bool
     */
    public function load($sId = null)
    {
        if (!is_null($sId)) {
            $this->setId($sId);
        }

        $this->_blIsLoaded = false;
        $aDbData = $this->getDbGateway()->load($this->getId());
        if ($aDbData) {
            $aData = array();
            foreach ($aDbData as $aRecord) {
                $aData[$aRecord['OEVATTBE_COUNTRYID']] = $aRecord['OEVATTBE_VATGROUPID'];
            }
            $this->setData($aData);
            $this->_blIsLoaded = true;
        }

        return $this->isLoaded();
    }

    /**
     * Method for loading article VAT group list. If loaded - returns true.
     *
     * @param string $sGroupId Group id.
     *
     * @return array
     */
    public function getArticlesAssignedToGroup($sGroupId = null)
    {
        $aData = array();
        $aDbData = $this->getDbGateway()->loadByGroupId($sGroupId);
        if ($aDbData) {
            foreach ($aDbData as $aRecord) {
                $aData[] = $aRecord['OEVATTBE_ARTICLEID'];
            }
        }

        return $aData;
    }
}
