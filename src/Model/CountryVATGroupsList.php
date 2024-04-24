<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EVatModule\Core\Model;
use OxidEsales\EVatModule\Model\DbGateway\CountryVATGroupsDbGateway;

/**
 * VAT Groups handling class
 */
class CountryVATGroupsList extends Model
{
    public function __construct(
        protected CountryVATGroupsDbGateway $dbGateway
    )
    {
    }

    /**
     * Selects and loads order payment history.
     *
     * @param string $sId Country id.
     *
     * @return array
     */
    public function load($sId = null)
    {
        if (!is_null($sId)) {
            $this->setId($sId);
        }

        $groupArticleCacheInvalidator = ContainerFacade::get(GroupArticleCacheInvalidator::class);

        $aGroups = array();
        $oGateway = $this->getDbGateway();
        $aGroupsData = $oGateway->getList($this->getId());
        if (is_array($aGroupsData) && count($aGroupsData)) {
            foreach ($aGroupsData as $aData) {
                /** @var CountryVATGroup $oGroup */
                $oGroup = oxNew(CountryVATGroup::class, $oGateway, $groupArticleCacheInvalidator);
                $oGroup->setId($aData['OEVATTBE_ID']);
                $oGroup->setData($aData);
                $aGroups[] = $oGroup;
            }
        }
        $this->setData($aGroups);

        return $aGroups;
    }

    /**
     * Returns all groups per country with key as country id and array of its groups as value.
     *
     * @return array
     */
    public function getList()
    {
        $groupArticleCacheInvalidator = ContainerFacade::get(GroupArticleCacheInvalidator::class);

        $aGroups = array();
        $oGateway = $this->getDbGateway();
        $aGroupsData = $oGateway->getList();
        if (is_array($aGroupsData) && count($aGroupsData)) {
            foreach ($aGroupsData as $aData) {
                /** @var CountryVATGroup $oGroup */
                $oGroup = oxNew(CountryVATGroup::class, $oGateway, $groupArticleCacheInvalidator);
                $oGroup->setId($aData['OEVATTBE_ID']);
                $oGroup->setData($aData);
                $aGroups[$aData['OEVATTBE_COUNTRYID']][] = $oGroup;
            }
        }

        return $aGroups;
    }
}
