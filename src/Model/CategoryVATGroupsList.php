<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model;

use OxidEsales\EVatModule\Core\Model;
use OxidEsales\EVatModule\Model\DbGateway\CategoryVATGroupsDbGateway;

/**
 * VAT Groups handling class
 */
class CategoryVATGroupsList extends Model
{
    /** @var array Model data. */
    protected $_aData = array();

    public function __construct(
        protected CategoryVATGroupsDbGateway $_oDbGateway
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
                    'OEVATTBE_CATEGORYID' => $this->getId(),
                    'OEVATTBE_COUNTRYID' => $sCountryId,
                    'OEVATTBE_VATGROUPID' => $sGroupId
                );
            }
        }

        $aData = array(
            'categoryid' => $this->getId(),
            'relations' => $aDbData
        );
        $this->getDbGateway()->save($aData);

        return $this->getId();
    }

    /**
     * Method for loading article VAT group list. If loaded - returns true.
     *
     * @param string $sId category id.
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
}
