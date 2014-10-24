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
 */
class Unit_oeVatTbe_models_oeVATTBECountryVATGroupsListTest extends OxidTestCase
{

    /**
     * Two Country Groups exits;
     * List is successfully loaded and array of groups is returned.
     */
    public function testLoadingGroupsListWhenGroupsExists()
    {
        $aGroup1Data = array(
            'OEVATTBE_ID' => 99,
            'OEVATTBE_COUNTRYID' => '8f241f11095410f38.37165361',
            'OEVATTBE_NAME' => 'Group Name',
            'OEVATTBE_DESCRIPTION' => 'Some description',
            'OEVATTBE_RATE' => '20.50',
            'OEVATTBE_TIMESTAMP' => '2014-05-05 18:00:00',
        );
        $aGroup2Data = $aGroup1Data;
        $aGroup2Data['OEVATTBE_ID'] = 100;
        $aData = array($aGroup1Data, $aGroup2Data);

        $oGateway = $this->_createStub('oeVATTBECountryVATGroupsDbGateway', array('getList' => $aData));

        $oGroup1 = $this->_createGroupObject($aGroup1Data);
        $oGroup2 = $this->_createGroupObject($aGroup2Data);

        /** @var oeVATTBECountryVATGroupsList $oGroupsList */
        $oGroupsList = oxNew('oeVATTBECountryVATGroupsList', $oGateway);

        $this->assertEquals(array($oGroup1, $oGroup2), $oGroupsList->load('someCountryId'));
    }

    /**
     * Two Country Groups for one country and one group for other country exits;
     * List is successfully loaded and array of groups is returned.
     */
    public function testFormingListOfAllCountriesGroupsList()
    {
        $aGroup1Data = array(
            'OEVATTBE_ID' => 99,
            'OEVATTBE_COUNTRYID' => 'a7c40f632e04633c9.47194042',
            'OEVATTBE_NAME' => 'Group Name',
            'OEVATTBE_DESCRIPTION' => 'Some description',
            'OEVATTBE_RATE' => '20.50',
            'OEVATTBE_TIMESTAMP' => '2014-05-05 18:00:00',
        );
        $aGroup2Data = $aGroup1Data;
        $aGroup2Data['OEVATTBE_ID'] = 100;

        $aGroup3Data = $aGroup1Data;
        $aGroup3Data['OEVATTBE_ID'] = 101;
        $aGroup3Data['OEVATTBE_COUNTRYID'] = '8f241f110957cb457.97820918';

        $aData = array($aGroup1Data, $aGroup2Data, $aGroup3Data);

        $oGateway = $this->_createStub('oeVATTBECountryVATGroupsDbGateway', array('getList' => $aData));

        $oGroup1 = $this->_createGroupObject($aGroup1Data, $oGateway);
        $oGroup2 = $this->_createGroupObject($aGroup2Data, $oGateway);
        $oGroup3 = $this->_createGroupObject($aGroup3Data, $oGateway);

        /** @var oeVATTBECountryVATGroupsList $oGroupsList */
        $oGroupsList = oxNew('oeVATTBECountryVATGroupsList', $oGateway);

        $aExpectedList = array(
            'a7c40f632e04633c9.47194042' => array($oGroup1, $oGroup2),
            '8f241f110957cb457.97820918' => array($oGroup3)
        );

        $this->assertEquals($aExpectedList, $oGroupsList->getList());
    }

    /**
     * Creates VAT Group object and sets given data to it.
     *
     * @param array                             $aData
     *
     * @return oeVATTBECountryVATGroup
     */
    protected function _createGroupObject($aData)
    {
        $oGroupsList = oeVATTBECountryVATGroup::createCountryVATGroup();
        $oGroupsList->setId($aData['OEVATTBE_ID']);
        $oGroupsList->setData($aData);

        return $oGroupsList;
    }

    /**
     * Tests creating of oeVATTBEArticleVATGroupsList.
     */
    public function testCreatingListWithCreationMethod()
    {
        $oList = oeVATTBECountryVATGroupsList::createCountryVATGroupsList();

        $this->assertInstanceOf('oeVATTBECountryVATGroupsList', $oList);
    }
}
