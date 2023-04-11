<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\VatGroups;

use PHPUnit\Framework\TestCase;

/**
 * Testing CountryVatGroups class.
 *
 * @covers CountryVatGroups
 * @covers CountryVATGroupsDbGateway
 * @covers CountryVATGroup
 * @covers CountryVATGroupsList
 */
class CountryVATGroupsEditingTest extends TestCase
{
    /**
     * Gives test cases to get country groups
     * Provides country to load and groups which should exist in that country.
     *
     * @return array
     */
    public function providerGetVatGroupsForCountry()
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
        /** @var oeVATTBECountryVatGroups $oVATTBECountryVatGroups */
        $oVATTBECountryVatGroups = oxNew('oeVATTBECountryVatGroups');
        $oVATTBECountryVatGroups->setEditObjectId($sCountryId);
        $aCountryVatGroups = $oVATTBECountryVatGroups->getVatGroups();

        /** @var oeVATTBECountryVATGroup $aCountryVatGroup */
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
        /** @var oeVATTBECountryVatGroups $oVATTBECountryVatGroups */
        $oVATTBECountryVatGroups = oxNew('oeVATTBECountryVatGroups');
        $oVATTBECountryVatGroups->setEditObjectId($sCountryId);
        $aCountryVatGroups = $oVATTBECountryVatGroups->getVatGroups();

        $this->assertSame(array(), $aCountryVatGroups);
    }

    /**
     * Test if changing VAT groups for Country works.
     */
    public function testChangeCountryVATGroups()
    {
        $this->setTablesForCleanup('oevattbe_countryvatgroups');

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

        $this->setRequestParameter('updateval', $aRequestParameters);

        $sAustriaId = 'a7c40f6320aeb2ec2.72885259';
        /** @var oeVATTBECountryVatGroups $oVATTBECountryVatGroups */
        $oVATTBECountryVatGroups = oxNew('oeVATTBECountryVatGroups');
        $oVATTBECountryVatGroups->setEditObjectId($sAustriaId);
        $oVATTBECountryVatGroups->changeCountryVATGroups();

        /** @var oeVATTBECountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        /** @var oeVATTBECountryVatGroup $oVATTBECountryVatGroup */
        $oVATTBECountryVatGroup = oxNew('oeVATTBECountryVatGroup', $oGateway);

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
        $this->setTablesForCleanup('oevattbe_countryvatgroups');

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

        $this->setRequestParameter('updateval', $aRequestParameters);

        $sAustriaId = 'a7c40f6320aeb2ec2.72885259';
        /** @var oeVATTBECountryVatGroups $oVATTBECountryVatGroups */
        $oVATTBECountryVatGroups = oxNew('oeVATTBECountryVatGroups');
        $oVATTBECountryVatGroups->setEditObjectId($sAustriaId);
        $oVATTBECountryVatGroups->changeCountryVATGroups();

        /** @var oeVATTBECountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        /** @var oeVATTBECountryVatGroup $oVATTBECountryVatGroup */
        $oVATTBECountryVatGroup = oxNew('oeVATTBECountryVatGroup', $oGateway);

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
        $this->setTablesForCleanup('oevattbe_countryvatgroups');

        $sCountryId = 'a7c40f632a0804ab5.18804076';
        /** @var oeVATTBECountryVatGroups $oVATTBECountryVatGroups */
        $oVATTBECountryVatGroups = oxNew('oeVATTBECountryVatGroups');
        $oVATTBECountryVatGroups->setEditObjectId($sCountryId);
        $aCountryVatGroups = $oVATTBECountryVatGroups->getVatGroups();

        $this->assertSame(2, count($aCountryVatGroups));

        $this->setRequestParameter('countryVATGroupId', '79');
        $oVATTBECountryVatGroups->deleteCountryVatGroup();

        $aCountryVatGroups = $oVATTBECountryVatGroups->getVatGroups();
        $this->assertSame(1, count($aCountryVatGroups));

        $this->setRequestParameter('countryVATGroupId', '80');
        $oVATTBECountryVatGroups->deleteCountryVatGroup();

        $aCountryVatGroups = $oVATTBECountryVatGroups->getVatGroups();
        $this->assertSame(0, count($aCountryVatGroups));
    }
}