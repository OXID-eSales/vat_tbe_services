<?php
/**
 * This file is part of OXID eSales eVAT module.
 *
 * OXID eSales eVAT module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales eVAT module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales eVAT module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */

namespace OxidEsales\EVatModule\Model;

use OxidEsales\EVatModule\Core\Model;
use OxidEsales\EVatModule\Model\DbGateway\CountryVATGroupsDbGateway;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * VAT Groups handling class
 */
class CountryVATGroupsList extends Model
{
    use ServiceContainer;

    public function __construct(
        protected CountryVATGroupsDbGateway $_oDbGateway
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

        $aGroups = array();
        $oGateway = $this->getDbGateway();
        $aGroupsData = $oGateway->getList($this->getId());
        if (is_array($aGroupsData) && count($aGroupsData)) {
            foreach ($aGroupsData as $aData) {
                /** @var CountryVATGroup $oGroup */
                $oGroup = clone $this->getServiceFromContainer(CountryVATGroup::class);
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
        $oGateway = $this->getDbGateway();
        $aGroupsData = $oGateway->getList();
        if (is_array($aGroupsData) && count($aGroupsData)) {
            foreach ($aGroupsData as $aData) {
                /** @var CountryVATGroup $oGroup */
                $oGroup = clone $this->getServiceFromContainer(CountryVATGroup::class);
                $oGroup->setId($aData['OEVATTBE_ID']);
                $oGroup->setData($aData);
                $aGroups[$aData['OEVATTBE_COUNTRYID']][] = $oGroup;
            }
        }

        return $aGroups;
    }
}
