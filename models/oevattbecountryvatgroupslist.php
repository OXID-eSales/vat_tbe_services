<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * VAT Groups handling class
 */
class oeVATTBECountryVATGroupsList extends oeVATTBEModel
{

    /**
     * Creates an instance of oeVATTBECountryVATGroupsList.
     *
     * @return oeVATTBECountryVATGroupsList;
     */
    public static function createInstance()
    {
        $oGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        $oList = oxNew('oeVATTBECountryVATGroupsList', $oGateway);

        return $oList;
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

        $aGroups = array();
        $oGateway = $this->_getDbGateway();
        $aGroupsData = $oGateway->getList($this->getId());
        if (is_array($aGroupsData) && count($aGroupsData)) {
            foreach ($aGroupsData as $aData) {
                /** @var oeVATTBECountryVATGroup $oGroup */
                $oGroup = oeVATTBECountryVATGroup::createInstance();
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
        $aGroups = array();
        $oGateway = $this->_getDbGateway();
        $aGroupsData = $oGateway->getList();
        if (is_array($aGroupsData) && count($aGroupsData)) {
            foreach ($aGroupsData as $aData) {
                /** @var oeVATTBECountryVATGroup $oGroup */
                $oGroup = oeVATTBECountryVATGroup::createInstance();
                $oGroup->setId($aData['OEVATTBE_ID']);
                $oGroup->setData($aData);
                $aGroups[$aData['OEVATTBE_COUNTRYID']][] = $oGroup;
            }
        }

        return $aGroups;
    }
}
