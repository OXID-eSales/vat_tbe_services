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
 * Testing oeVATTBECountryVATGroupsList class.
 *
 * @covers oeVATTBECountryVATGroupsList
 * @covers oeVATTBECountryVATGroup
 * @covers oeVATTBECountryVATGroupsDbGateway
 */
class Integration_oeVatTbe_VATGroups_oeVATTBECountryVATGroupsListTest extends OxidTestCase
{
    /**
     * Two Country Groups exits;
     * List is successfully loaded and array of groups is returned.
     */
    public function testLoadingGroupsListWhenGroupsExists()
    {
        $aGroupData = array(
            'OEVATTBE_COUNTRYID' => '8f241f11095410f38.37165361',
            'OEVATTBE_NAME' => 'Group Name',
            'OEVATTBE_DESCRIPTION' => 'Some description',
            'OEVATTBE_RATE' => '20.50',
            'OEVATTBE_TIMESTAMP' => '2014-10-22 11:19:20',
        );

        /** @var oeVATTBECountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');

        $oGroup1 = $this->_createGroupObject($aGroupData);
        $oGroup2 = $this->_createGroupObject($aGroupData);

        /** @var oeVATTBECountryVATGroupsList $oGroupsList */
        $oGroupsList = oxNew('oeVATTBECountryVATGroupsList', $oGateway);

        $this->assertEquals(array($oGroup1, $oGroup2), $oGroupsList->load('8f241f11095410f38.37165361'));
    }

    /**
     * No Country Groups exits;
     * List is successfully loaded and empty array is returned.
     */
    public function testLoadingGroupsListWhenNoGroupsExists()
    {
        /** @var oeVATTBECountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');

        /** @var oeVATTBECountryVATGroupsList $oGroupsList */
        $oGroupsList = oxNew('oeVATTBECountryVATGroupsList', $oGateway);

        $this->assertEquals(array(), $oGroupsList->load('NonExistingCountryId'));
    }

    /**
     * Creates VAT Group object and sets given data to it.
     *
     * @param array                      $aData
     *
     * @return oeVATTBECountryVATGroup
     */
    protected function _createGroupObject($aData)
    {
        $oGroup = oeVATTBECountryVATGroup::createCountryVATGroup();
        $oGroup->setId($aData['OEVATTBE_ID']);
        $oGroup->setData($aData);
        $aData['OEVATTBE_ID'] = $oGroup->save();
        $oGroup->setData($aData);

        return $oGroup;
    }
}
