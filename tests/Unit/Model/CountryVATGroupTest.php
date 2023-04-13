<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EVatModule\Model\CountryVATGroup;
use OxidEsales\EVatModule\Model\DbGateway\CountryVATGroupsDbGateway;
use OxidEsales\EVatModule\Model\GroupArticleCacheInvalidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Testing CountryVATGroup class.
 *
 * @covers CountryVATGroup
 */
class CountryVATGroupTest extends TestCase
{
    use ContainerTrait;

    /**
     * Information is set to Group entity;
     * Correct information array is passed to gateway for saving.
     */
    public function testSavingVATGroup()
    {
        $aExpectedData = [
            'oevattbe_countryid'   => '8f241f11095410f38.37165361',
            'oevattbe_name'        => 'Group Name',
            'oevattbe_description' => 'Some description',
            'oevattbe_rate'        => 20.50
        ];

        $oGateway = $this->createPartialMock(CountryVATGroupsDbGateway::class, ['save']);
        $oGateway->expects($this->once())->method('save')->with($aExpectedData);

        /** @var CountryVATGroup $oGroup */
        $oGroup = oxNew(CountryVATGroup::class, $oGateway);

        $oGroup->setCountryId('8f241f11095410f38.37165361');
        $oGroup->setName('Group Name');
        $oGroup->setDescription('Some description');
        $oGroup->setRate(20.50);

        $oGroup->save();
    }

    /**
     * Information is set to Group entity with id set;
     * Correct information array is passed to gateway for saving.
     */
    public function testUpdatingVATGroup()
    {
        $aExpectedData = [
            'oevattbe_id'          => '999',
            'oevattbe_countryid'   => '8f241f11095410f38.37165361',
            'oevattbe_name'        => 'Group Name',
            'oevattbe_description' => 'Some description',
            'oevattbe_rate'        => 20.50
        ];

        $oGateway = $this->createPartialMock(CountryVATGroupsDbGateway::class, ['save']);
        $oGateway->expects($this->once())->method('save')->with($aExpectedData);

        /** @var CountryVATGroup $oGroup */
        $oGroup = oxNew(CountryVATGroup::class, $oGateway);

        $oGroup->setId('999');
        $oGroup->setCountryId('8f241f11095410f38.37165361');
        $oGroup->setName('Group Name');
        $oGroup->setDescription('Some description');
        $oGroup->setRate(20.50);

        $oGroup->save();
    }

    /**
     * No information is set to group;
     * Correct information array is passed to gateway for saving.
     */
    public function testSavingVATGroupWithNoData()
    {
        $oGateway = $this->createPartialMock(CountryVATGroupsDbGateway::class, ['save']);
        $oGateway->expects($this->once())->method('save')->with(null);

        /** @var CountryVATGroup $oGroup */
        $oGroup = oxNew(CountryVATGroup::class, $oGateway);

        $oGroup->save();
    }

    /**
     * Group information is provided by gateway;
     * All information is correctly taken.
     */
    public function testLoadingVATGroup()
    {
        $aData = [
            'OEVATTBE_ID'          => 99,
            'OEVATTBE_COUNTRYID'   => '8f241f11095410f38.37165361',
            'OEVATTBE_NAME'        => 'Group Name',
            'OEVATTBE_DESCRIPTION' => 'Some description',
            'OEVATTBE_RATE'        => '20.50',
            'OEVATTBE_TIMESTAMP'   => '2014-05-05 18:00:00',
        ];

        $oGateway = $this->createStub(CountryVATGroupsDbGateway::class);
        $oGateway->method('load')->willReturn($aData);

        /** @var CountryVATGroup $oGroup */
        $oGroup = oxNew(CountryVATGroup::class, $oGateway);
        $oGroup->load(99);

        $this->assertSame('8f241f11095410f38.37165361', $oGroup->getCountryId());
        $this->assertSame('Group Name', $oGroup->getName());
        $this->assertSame('Some description', $oGroup->getDescription());
        $this->assertSame('20.50', $oGroup->getRate());
    }

    /**
     * Tests creating of oeVATTBEArticleVATGroupsList.
     */
    public function testCreatingGroupWithCreationMethod()
    {
        $oGroup = $this->get(CountryVATGroup::class);

        $this->assertInstanceOf(CountryVATGroup::class, $oGroup);
    }

    /**
     * Tests invalidating cache on group save event.
     */
    public function testInvalidatingCacheOnGroupSaving()
    {
        /** @var CountryVATGroupsDbGateway|MockObject $oGateway */
        $oGateway = $this->createStub(CountryVATGroupsDbGateway::class);
        $oGateway->method('save')->willReturn('groupId');

        $oInvalidator = $this->createPartialMock(GroupArticleCacheInvalidator::class, ['invalidate']);
        $oInvalidator->expects($this->atLeastOnce())->method('invalidate')->with('groupId');

        /** @var CountryVATGroup $oGroup */
        $oGroup = oxNew(CountryVATGroup::class, $oGateway);
        $oGroup->setVATGroupArticleCacheInvalidator($oInvalidator);
        $oGroup->setId('groupId');

        $oGroup->save();
    }

    /**
     * Tests invalidating cache on group save event.
     */
    public function testInvalidatingCacheOnGroupDeletion()
    {
        $oGateway = $this->createStub(CountryVATGroupsDbGateway::class);
        $oGateway->method('save')->willReturn('groupId');

        $oInvalidator = $this->createPartialMock(GroupArticleCacheInvalidator::class, ['invalidate']);
        $oInvalidator->expects($this->atLeastOnce())->method('invalidate')->with('groupId');

        /** @var CountryVATGroup $oGroup */
        $oGroup = oxNew(CountryVATGroup::class, $oGateway);
        $oGroup->setVATGroupArticleCacheInvalidator($oInvalidator);
        $oGroup->setId('groupId');

        $oGroup->delete();
    }
}
