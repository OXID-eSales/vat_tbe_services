<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Model;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EVatModule\Model\CountryVATGroup;
use OxidEsales\EVatModule\Model\CountryVATGroupsList;
use OxidEsales\EVatModule\Model\DbGateway\CountryVATGroupsDbGateway;
use PHPUnit\Framework\TestCase;

/**
 * Testing oeVATTBECountryVATGroupsList class.
 */
class CountryVATGroupsListTest extends TestCase
{
    use ContainerTrait;

    /**
     * Two Country Groups exits;
     * List is successfully loaded and array of groups is returned.
     */
    public function testLoadingGroupsListWhenGroupsExists()
    {
        $aGroup1Data = [
            'OEVATTBE_ID'          => 99,
            'OEVATTBE_COUNTRYID'   => '8f241f11095410f38.37165361',
            'OEVATTBE_NAME'        => 'Group Name',
            'OEVATTBE_DESCRIPTION' => 'Some description',
            'OEVATTBE_RATE'        => '20.50',
            'OEVATTBE_TIMESTAMP'   => '2014-05-05 18:00:00',
        ];
        $aGroup2Data = $aGroup1Data;
        $aGroup2Data['OEVATTBE_ID'] = 100;
        $aData = [$aGroup1Data, $aGroup2Data];

        $oGateway = $this->createStub(CountryVATGroupsDbGateway::class);
        $oGateway->method('getList')->willReturn($aData);

        $oGroup1 = $this->_createGroupObject($aGroup1Data, $oGateway);
        $oGroup2 = $this->_createGroupObject($aGroup2Data, $oGateway);

        /** @var CountryVATGroupsList $oGroupsList */
        $oGroupsList = oxNew(CountryVATGroupsList::class, $oGateway);

        $this->assertEquals([$oGroup1, $oGroup2], $oGroupsList->load('someCountryId'));
    }

    /**
     * Two Country Groups for one country and one group for other country exits;
     * List is successfully loaded and array of groups is returned.
     */
    public function testFormingListOfAllCountriesGroupsList()
    {
        $aGroup1Data = [
            'OEVATTBE_ID'          => 99,
            'OEVATTBE_COUNTRYID'   => 'a7c40f632e04633c9.47194042',
            'OEVATTBE_NAME'        => 'Group Name',
            'OEVATTBE_DESCRIPTION' => 'Some description',
            'OEVATTBE_RATE'        => '20.50',
            'OEVATTBE_TIMESTAMP'   => '2014-05-05 18:00:00',
        ];
        $aGroup2Data = $aGroup1Data;
        $aGroup2Data['OEVATTBE_ID'] = 100;

        $aGroup3Data = $aGroup1Data;
        $aGroup3Data['OEVATTBE_ID'] = 101;
        $aGroup3Data['OEVATTBE_COUNTRYID'] = '8f241f110957cb457.97820918';

        $aData = [$aGroup1Data, $aGroup2Data, $aGroup3Data];

        $oGateway = $this->createStub(CountryVATGroupsDbGateway::class);
        $oGateway->method('getList')->willReturn($aData);

        $oGroup1 = $this->_createGroupObject($aGroup1Data, $oGateway);
        $oGroup2 = $this->_createGroupObject($aGroup2Data, $oGateway);
        $oGroup3 = $this->_createGroupObject($aGroup3Data, $oGateway);

        /** @var CountryVATGroupsList $oGroupsList */
        $oGroupsList = oxNew(CountryVATGroupsList::class, $oGateway);

        $aExpectedList = [
            'a7c40f632e04633c9.47194042' => [$oGroup1, $oGroup2],
            '8f241f110957cb457.97820918' => [$oGroup3]
        ];

        $this->assertEquals($aExpectedList, $oGroupsList->getList());
    }

    /**
     * Creates VAT Group object and sets given data to it.
     *
     * @param array $aData
     *
     * @return CountryVATGroup
     */
    protected function _createGroupObject($aData, $oGateway)
    {
        $oGroupsList = oxNew(CountryVATGroup::class, $oGateway);
        $oGroupsList->setId($aData['OEVATTBE_ID']);
        $oGroupsList->setData($aData);

        return $oGroupsList;
    }

    /**
     * Tests creating of oeVATTBEArticleVATGroupsList.
     */
    public function testCreatingListWithCreationMethod()
    {
        $oList = $this->get(CountryVATGroupsList::class);

        $this->assertInstanceOf(CountryVATGroupsList::class, $oList);
    }
}
