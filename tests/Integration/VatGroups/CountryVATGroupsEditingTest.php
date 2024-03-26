<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\VatGroups;

use OxidEsales\EVatModule\Controller\Admin\CountryVatGroups;
use OxidEsales\EVatModule\Model\CountryVATGroup;
use OxidEsales\EVatModule\Model\DbGateway\CountryVATGroupsDbGateway;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Testing CountryVatGroups class.
 */
class CountryVATGroupsEditingTest extends BaseTestCase
{
    /**
     * Gives test cases to get country groups
     * Provides country to load and groups which should exist in that country.
     *
     * @return array
     */
    public static function providerGetVatGroupsForCountry()
    {
        $sAustriaId = 'a7c40f6320aeb2ec2.72885259';
        $aAustriaGroups = array(56 => '', 57 => '', 58 => '');

        $sGermanyId = 'a7c40f631fc920687.20179984';
        $aGermanyGroups = array(10 => '', 11 => '');

        return array(
            array($sAustriaId, $aAustriaGroups),
            array($sGermanyId, $aGermanyGroups),
        );
    }

    /**
     * Test if getting VAT groups for Country works.
     *
     * @param string $sCountryId      country id to load groups.
     * @param array  $aExpectedGroups groups which should exist in given country.
     *
     * @dataProvider providerGetVatGroupsForCountry
     */
    public function testGetVatGroupsForCountry($sCountryId, $aExpectedGroups)
    {
        /** @var CountryVatGroups $oVATTBECountryVatGroups */
        $oVATTBECountryVatGroups = oxNew(CountryVatGroups::class);
        $oVATTBECountryVatGroups->setEditObjectId($sCountryId);
        $aCountryVatGroups = $oVATTBECountryVatGroups->getVatGroups();

        /** @var CountryVATGroup $aCountryVatGroup */
        foreach ($aCountryVatGroups as $aCountryVatGroup) {
            $aResultGroups[$aCountryVatGroup->getId()] = '';
        }

        $this->assertEquals($aExpectedGroups, $aResultGroups);
    }

    /**
     * Test if do not load groups for Country without groups.
     */
    public function testGetVatGroupsForCountryWithoutGroups()
    {
        $sCountryId = 'a7c40f6321c6f6109.43859248';
        /** @var CountryVatGroups $oVATTBECountryVatGroups */
        $oVATTBECountryVatGroups = oxNew(CountryVatGroups::class);
        $oVATTBECountryVatGroups->setEditObjectId($sCountryId);
        $aCountryVatGroups = $oVATTBECountryVatGroups->getVatGroups();

        $this->assertSame(array(), $aCountryVatGroups);
    }

    /**
     * Test if changing VAT groups for Country works.
     */
    public function testChangeCountryVATGroups()
    {
        $fVATRate = 55.5;
        $sExpectedVATRate = '55.50';

        $iGroupId = 56;
        $aRequestParameters[$iGroupId]['oevattbe_id'] = $iGroupId;
        $aRequestParameters[$iGroupId]['oevattbe_name'] = 'some name';
        $aRequestParameters[$iGroupId]['oevattbe_rate'] = $fVATRate;
        $aRequestParameters[$iGroupId]['oevattbe_description'] = 'some description';

        $iGroupId = 57;
        $aRequestParameters[$iGroupId]['oevattbe_id'] = $iGroupId;
        $aRequestParameters[$iGroupId]['oevattbe_name'] = 'some other name';
        $aRequestParameters[$iGroupId]['oevattbe_rate'] = $fVATRate;
        $aRequestParameters[$iGroupId]['oevattbe_description'] = 'some other description';

        $_POST['updateval'] = $aRequestParameters;

        $sAustriaId = 'a7c40f6320aeb2ec2.72885259';
        /** @var CountryVatGroups $oVATTBECountryVatGroups */
        $oVATTBECountryVatGroups = oxNew(CountryVatGroups::class);
        $oVATTBECountryVatGroups->setEditObjectId($sAustriaId);
        $oVATTBECountryVatGroups->changeCountryVATGroups();

        /** @var CountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew(CountryVATGroupsDbGateway::class);
        /** @var CountryVatGroup $oVATTBECountryVatGroup */
        $oVATTBECountryVatGroup = oxNew(CountryVatGroup::class, $oGateway);

        foreach ($aRequestParameters as $iExpectedGroupId => $aExpectedCountryVatGroup) {
            $oVATTBECountryVatGroup->load($iExpectedGroupId);
            $this->assertSame($aExpectedCountryVatGroup['oevattbe_name'], $oVATTBECountryVatGroup->getName());
            $this->assertSame($sExpectedVATRate, $oVATTBECountryVatGroup->getRate());
            $this->assertSame($aExpectedCountryVatGroup['oevattbe_description'], $oVATTBECountryVatGroup->getDescription());
        }
    }
    /**
     * Test if changing VAT groups for Country works when one group has missing required parameter.
     */
    public function testChangeCountryVATGroupsWhenOneGroupMissName()
    {
        $iGroupId = 56;
        $aRequestParameters[$iGroupId]['oevattbe_id'] = $iGroupId;
        $aRequestParameters[$iGroupId]['oevattbe_name'] = '';
        $aRequestParameters[$iGroupId]['oevattbe_rate'] = 55.5;
        $aRequestParameters[$iGroupId]['oevattbe_description'] = 'some description';

        $iGroupId = 57;
        $aRequestParameters[$iGroupId]['oevattbe_id'] = $iGroupId;
        $aRequestParameters[$iGroupId]['oevattbe_name'] = 'some other name';
        $aRequestParameters[$iGroupId]['oevattbe_rate'] = 55.5;
        $aRequestParameters[$iGroupId]['oevattbe_description'] = 'some other description';

        $_POST['updateval'] = $aRequestParameters;

        $sAustriaId = 'a7c40f6320aeb2ec2.72885259';
        /** @var CountryVatGroups $oVATTBECountryVatGroups */
        $oVATTBECountryVatGroups = oxNew(CountryVatGroups::class);
        $oVATTBECountryVatGroups->setEditObjectId($sAustriaId);
        $oVATTBECountryVatGroups->changeCountryVATGroups();

        /** @var CountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew(CountryVATGroupsDbGateway::class);
        /** @var CountryVatGroup $oVATTBECountryVatGroup */
        $oVATTBECountryVatGroup = oxNew(CountryVatGroup::class, $oGateway);

        $oVATTBECountryVatGroup->load(57);
        $this->assertSame('some other name', $oVATTBECountryVatGroup->getName());
        $this->assertSame('55.50', $oVATTBECountryVatGroup->getRate());
        $this->assertSame('some other description', $oVATTBECountryVatGroup->getDescription());

        $oVATTBECountryVatGroup->load(56);
        $this->assertNotSame('', $oVATTBECountryVatGroup->getName());
        $this->assertNotSame('55.50', $oVATTBECountryVatGroup->getRate());
        $this->assertNotSame('some description', $oVATTBECountryVatGroup->getDescription());
    }

    /**
     * Test if country VAT groups might be deleted.
     */
    public function testDeleteCountryVatGroup()
    {
        $sCountryId = 'a7c40f632a0804ab5.18804076';
        /** @var CountryVatGroups $oVATTBECountryVatGroups */
        $oVATTBECountryVatGroups = oxNew(CountryVatGroups::class);
        $oVATTBECountryVatGroups->setEditObjectId($sCountryId);
        $aCountryVatGroups = $oVATTBECountryVatGroups->getVatGroups();

        $this->assertSame(2, count($aCountryVatGroups));

        $_POST['countryVATGroupId'] = '79';
        $oVATTBECountryVatGroups->deleteCountryVatGroup();

        $aCountryVatGroups = $oVATTBECountryVatGroups->getVatGroups();
        $this->assertSame(1, count($aCountryVatGroups));

        $_POST['countryVATGroupId'] = '80';
        $oVATTBECountryVatGroups->deleteCountryVatGroup();

        $aCountryVatGroups = $oVATTBECountryVatGroups->getVatGroups();
        $this->assertSame(0, count($aCountryVatGroups));
    }
}
