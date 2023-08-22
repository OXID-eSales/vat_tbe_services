<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\VatGroups;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EVatModule\Model\CountryVATGroup;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Testing CountryVATGroupsList class.
 */
class CountryVATGroupTest extends BaseTestCase
{
    use ContainerTrait;

    /**
     * Tests saving group to database.
     *
     * @return string
     */
    public function testSavingGroup()
    {
        $oGroup = $this->get(CountryVATGroup::class);

        $oGroup->setCountryId('8f241f11095410f38.37165361');
        $oGroup->setName('Group Name');
        $oGroup->setDescription('Some description');
        $oGroup->setRate('20.50');

        $sGroupId = $oGroup->save();

        $oExpectedGroup = $this->get(CountryVATGroup::class);
        $oExpectedGroup->load($sGroupId);

        $this->assertEquals($oGroup->getCountryId(), $oExpectedGroup->getCountryId());
        $this->assertEquals($oGroup->getName(), $oExpectedGroup->getName());
        $this->assertEquals($oGroup->getDescription(), $oExpectedGroup->getDescription());
        $this->assertEquals($oGroup->getRate(), $oExpectedGroup->getRate());

        return $sGroupId;
    }

    /**
     * Tests updating group without loading it, but providing its id and all info instead.
     *
     * @param string $sGroupId
     *
     * @depends testSavingGroup
     */
    public function testUpdatingGroupWithoutLoadingIt($sGroupId)
    {
        $oGroup = $this->get(CountryVATGroup::class);

        $oGroup->setId($sGroupId);
        $oGroup->setCountryId('8f241f11095410f38.37165361');
        $oGroup->setName('New Group Name');
        $oGroup->setDescription('New description');
        $oGroup->setRate('20.60');

        $oGroup->save();

        $oExpectedGroup = $this->get(CountryVATGroup::class);
        $oExpectedGroup->load($sGroupId);

        $this->assertEquals($oGroup->getCountryId(), $oExpectedGroup->getCountryId());
        $this->assertEquals($oGroup->getName(), $oExpectedGroup->getName());
        $this->assertEquals($oGroup->getDescription(), $oExpectedGroup->getDescription());
        $this->assertEquals($oGroup->getRate(), $oExpectedGroup->getRate());
    }
}
