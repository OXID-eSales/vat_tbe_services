<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\VatGroups;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EVatModule\Model\ArticleVATGroupsList;
use OxidEsales\EVatModule\Model\DbGateway\CountryVATGroupsDbGateway;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Test class for CountryVATGroupsDbGateway.
 */
class CountryVATGroupsDbGatewayTest extends BaseTestCase
{
    use ContainerTrait;

    /**
     * Testing VAT Group saving to database.
     *
     * @return string
     */
    public function testSavingVATGroupToDatabase()
    {
        $oVatGroupsGateway = oxNew(CountryVATGroupsDbGateway::class);
        $aData = array(
            'oevattbe_countryid' => '8f241f11095410f38.37165361',
            'oevattbe_name' => 'Group Name',
            'oevattbe_description' => 'Some description',
            'oevattbe_rate' => 20.50
        );
        $sGroupId = $oVatGroupsGateway->save($aData);
        $this->assertTrue(is_string($sGroupId));

        return $sGroupId;
    }

    /**
     * Testing VAT Group loading from database.
     *
     * @param string $sGroupId
     *
     * @depends testSavingVATGroupToDatabase
     *
     * @return string
     */
    public function testVATGroupLoading($sGroupId)
    {
        $oVatGroupsGateway = oxNew(CountryVATGroupsDbGateway::class);
        $aData = $oVatGroupsGateway->load($sGroupId);

        $aExpectedData = array(
            'OEVATTBE_ID' => $sGroupId,
            'OEVATTBE_COUNTRYID' => '8f241f11095410f38.37165361',
            'OEVATTBE_NAME' => 'Group Name',
            'OEVATTBE_DESCRIPTION' => 'Some description',
            'OEVATTBE_RATE' => '20.50',
            'OEVATTBE_TIMESTAMP' => $aData['OEVATTBE_TIMESTAMP'],
        );

        $this->assertSame($aExpectedData, $aData);

        return $sGroupId;
    }

    /**
     * Testing deletion of VAT Group from database.
     *
     * @param string $sGroupId
     *
     * @depends testVATGroupLoading
     */
    public function testDeletingVATGroupList($sGroupId)
    {
        $oVatGroupsGateway = oxNew(CountryVATGroupsDbGateway::class);
        $oVatGroupsGateway->delete($sGroupId);

        $this->assertSame(array(), $oVatGroupsGateway->load($sGroupId));

        //No VAT Groups exists for specific country, empty groups list should be returned.
        $aGroupsList = $oVatGroupsGateway->getList('8f241f11095410f38.37165361');

        $this->assertEquals(array(), $aGroupsList);
    }

    /**
     * Testing deletion of article and groups dependencies on group deletion.
     */
    public function testDeletingVATGroupAndArticlesRelations()
    {
        $oRelationsList = $this->get(ArticleVATGroupsList::class);
        $oRelationsList->setId('articleid');
        $oRelationsList->setData(array('germanyid' => '10', 'lithuaniaid' => '12'));
        $oRelationsList->save();

        $oVatGroupsGateway = oxNew(CountryVATGroupsDbGateway::class);
        $oVatGroupsGateway->delete('12');

        $oRelationsList->load('articleid');
        $this->assertSame(array('germanyid' => '10'), $oRelationsList->getData());
    }

    /**
     * Test loading non existing VAT Group.
     */
    public function testLoadingEmptyVATGroup()
    {
        $oVatGroupsGateway = oxNew(CountryVATGroupsDbGateway::class);
        $this->assertSame(array(), $oVatGroupsGateway->load('non_existing_group'));
    }

    /**
     * Test deleting non existing VAT Group.
     */
    public function testDeletingEmptyVATGroup()
    {
        $oVatGroupsGateway = oxNew(CountryVATGroupsDbGateway::class);
        $this->assertNotSame(false, $oVatGroupsGateway->delete('non_existing_group'));
    }

    /**
     * VAT Groups exists for specific country;
     * Groups list is loaded with correct information for this country.
     */
    public function testLoadingGroupsListForCountryWhenGroupsExist()
    {
        $oVatGroupsGateway = oxNew(CountryVATGroupsDbGateway::class);
        $aData = array(
            'OEVATTBE_COUNTRYID' => '8f241f11095410f38.37165361',
            'OEVATTBE_NAME' => 'Group Name',
            'OEVATTBE_DESCRIPTION' => 'Some description',
            'OEVATTBE_RATE' => 20.50
        );
        $sGroupId1 = $oVatGroupsGateway->save($aData);
        $sGroupId2 = $oVatGroupsGateway->save($aData);

        $oVatGroupsGateway = oxNew(CountryVATGroupsDbGateway::class);
        $aGroupsList = $oVatGroupsGateway->getList('8f241f11095410f38.37165361');

        $aData1 = $aData;
        $aData1['OEVATTBE_ID'] = $sGroupId1;
        $aData1['OEVATTBE_TIMESTAMP'] = $aGroupsList[0]['OEVATTBE_TIMESTAMP'];

        $aData2 = $aData;
        $aData2['OEVATTBE_ID'] = $sGroupId2;
        $aData2['OEVATTBE_TIMESTAMP'] = $aGroupsList[1]['OEVATTBE_TIMESTAMP'];

        $this->assertEquals(array($aData1, $aData2), $aGroupsList);
    }

    /**
     * VAT Groups exists for specific country;
     * Groups list is loaded with correct information for this country.
     */
    public function testLoadingGroupsListWhenGroupsExistAndNoCountryIsPassed()
    {
        $oVatGroupsGateway = oxNew(CountryVATGroupsDbGateway::class);
        $aGroupsList = $oVatGroupsGateway->getList();

        $this->assertNotEmpty($aGroupsList);
    }
}
