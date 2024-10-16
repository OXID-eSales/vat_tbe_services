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
        $sGroupId = $this->createVatGroup();
        $this->assertTrue(is_string($sGroupId));

        return $sGroupId;
    }

    /**
     * Testing VAT Group loading from database.
     *
     * @param string $sGroupId
     *
     * @return string
     */
    public function testVATGroupLoading()
    {
        $sGroupId = $this->createVatGroup();

        $oVatGroupsGateway = oxNew(CountryVATGroupsDbGateway::class);
        $aData = $oVatGroupsGateway->load($sGroupId);

        $aExpectedData = [
            'OEVATTBE_ID'          => (int)$sGroupId,
            'OEVATTBE_COUNTRYID'   => '8f241f11095410f38.37165361',
            'OEVATTBE_NAME'        => 'Group Name',
            'OEVATTBE_DESCRIPTION' => 'Some description',
            'OEVATTBE_RATE'        => '20.50',
            'OEVATTBE_TIMESTAMP'   => $aData['OEVATTBE_TIMESTAMP'],
        ];

        $this->assertEquals($aExpectedData, $aData);

        return $sGroupId;
    }

    /**
     * Testing deletion of VAT Group from database.
     *
     * @param string $sGroupId
     */
    public function testDeletingVATGroupList()
    {
        $sGroupId = $this->createVatGroup();

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
        $this->assertEquals(array('germanyid' => 10), $oRelationsList->getData());
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
        $this->assertTrue($oVatGroupsGateway->delete('non_existing_group'));
    }

    /**
     * VAT Groups exists for specific country;
     * Groups list is loaded with correct information for this country.
     */
    public function testLoadingGroupsListForCountryWhenGroupsExist()
    {
        $oVatGroupsGateway = oxNew(CountryVATGroupsDbGateway::class);
        $aData = [
            'OEVATTBE_COUNTRYID'   => '8f241f11095410f38.37165361',
            'OEVATTBE_NAME'        => 'Group Name',
            'OEVATTBE_DESCRIPTION' => 'Some description',
            'OEVATTBE_RATE'        => 20.50
        ];
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

    protected function createVatGroup(): string
    {
        $oVatGroupsGateway = oxNew(CountryVATGroupsDbGateway::class);
        $aData = [
            'OEVATTBE_COUNTRYID'   => '8f241f11095410f38.37165361',
            'OEVATTBE_NAME'        => 'Group Name',
            'OEVATTBE_DESCRIPTION' => 'Some description',
            'OEVATTBE_RATE'        => 20.50
        ];

        return $oVatGroupsGateway->save($aData);
    }
}
