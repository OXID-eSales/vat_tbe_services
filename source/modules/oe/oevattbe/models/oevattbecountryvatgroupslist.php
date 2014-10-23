<?php
/**
 * This file is part of OXID eSales VAT TBE module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales VAT TBE module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
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
    public static function createCountryVATGroupsList()
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
                $oGroup = oxNew('oeVATTBECountryVATGroup', $oGateway);
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
                $oGroup = oxNew('oeVATTBECountryVATGroup', $oGateway);
                $oGroup->setId($aData['OEVATTBE_ID']);
                $oGroup->setData($aData);
                $aGroups[$aData['OEVATTBE_COUNTRYID']][] = $oGroup;
            }
        }

        return $aGroups;
    }
}
