<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\VatGroups;

use PHPUnit\Framework\TestCase;

/**
 * Testing CountryVATGroupsList class.
 *
 * @covers CountryVATGroupsList
 * @covers CountryVATGroup
 * @covers CountryVATGroupsDbGateway
 */
class CountryVATGroupsListTest extends TestCase
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
     * @param array $aData
     *
     * @return oeVATTBECountryVATGroup
     */
    protected function _createGroupObject($aData)
    {
        $oGroup = oeVATTBECountryVATGroup::createInstance();
        $oGroup->setId($aData['OEVATTBE_ID']);
        $oGroup->setData($aData);
        $aData['OEVATTBE_ID'] = $oGroup->save();
        $oGroup->setData($aData);

        return $oGroup;
    }
}
