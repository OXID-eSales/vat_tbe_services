<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\VatGroups;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EVatModule\Model\CountryVATGroup;
use OxidEsales\EVatModule\Model\CountryVATGroupsList;
use OxidEsales\EVatModule\Model\DbGateway\CountryVATGroupsDbGateway;
use OxidEsales\EVatModule\Model\GroupArticleCacheInvalidator;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Testing CountryVATGroupsList class.
 */
class CountryVATGroupsListTest extends BaseTestCase
{
    use ContainerTrait;

    public function setUp(): void
    {
        parent::setUp();

        ContainerFactory::resetContainer();
    }

    /**
     * Two Country Groups exits;
     * List is successfully loaded and array of groups is returned.
     */
    public function testLoadingGroupsListWhenGroupsExists()
    {
        $aGroupData = [
            'OEVATTBE_ID'          => '',
            'OEVATTBE_COUNTRYID'   => '8f241f11095410f38.37165361',
            'OEVATTBE_NAME'        => 'Group Name',
            'OEVATTBE_DESCRIPTION' => 'Some description',
            'OEVATTBE_RATE'        => '20.50',
            'OEVATTBE_TIMESTAMP'   => '2014-10-22 11:19:20',
        ];

        /** @var CountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew(CountryVATGroupsDbGateway::class);

        $oGroup1 = $this->createGroupObject($aGroupData, $oGateway);
        $oGroup2 = $this->createGroupObject($aGroupData, $oGateway);

        /** @var CountryVATGroupsList $oGroupsList */
        $oGroupsList = oxNew(CountryVATGroupsList::class, $oGateway);

        $this->assertEquals(array($oGroup1, $oGroup2), $oGroupsList->load('8f241f11095410f38.37165361'));
    }

    /**
     * No Country Groups exits;
     * List is successfully loaded and empty array is returned.
     */
    public function testLoadingGroupsListWhenNoGroupsExists()
    {
        /** @var CountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew(CountryVATGroupsDbGateway::class);

        /** @var CountryVATGroupsList $oGroupsList */
        $oGroupsList = oxNew(CountryVATGroupsList::class, $oGateway);

        $this->assertEquals(array(), $oGroupsList->load('NonExistingCountryId'));
    }

    /**
     * Creates VAT Group object and sets given data to it.
     *
     * @param array $aData
     *
     * @return CountryVATGroup
     */
    protected function createGroupObject($aData, $oGateway)
    {
        /** @var GroupArticleCacheInvalidator $groupArticleCacheInvalidator */
        $groupArticleCacheInvalidator = $this->get(GroupArticleCacheInvalidator::class);

        $oGroup = oxNew(CountryVATGroup::class, $oGateway, $groupArticleCacheInvalidator);
        $oGroup->setId($aData['OEVATTBE_ID']);
        $oGroup->setData($aData);
        $aData['OEVATTBE_ID'] = $oGroup->save();
        $oGroup->setData($aData);

        return $oGroup;
    }
}
