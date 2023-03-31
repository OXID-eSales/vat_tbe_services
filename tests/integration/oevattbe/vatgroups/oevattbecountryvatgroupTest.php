<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Testing oeVATTBECountryVATGroupsList class.
 *
 * @covers oeVATTBECountryVATGroupsList
 * @covers oeVATTBECountryVATGroup
 * @covers oeVATTBECountryVATGroupsDbGateway
 */
class Integration_oeVatTbe_VATGroups_oeVATTBECountryVATGroupTest extends OxidTestCase
{
    /**
     * Tests saving group to database.
     *
     * @return string
     */
    public function testSavingGroup()
    {
        $oGroup = oeVATTBECountryVATGroup::createInstance();

        $oGroup->setCountryId('8f241f11095410f38.37165361');
        $oGroup->setName('Group Name');
        $oGroup->setDescription('Some description');
        $oGroup->setRate('20.50');

        $sGroupId = $oGroup->save();

        $oExpectedGroup = oeVATTBECountryVATGroup::createInstance();
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
        $oGroup = oeVATTBECountryVATGroup::createInstance();

        $oGroup->setId($sGroupId);
        $oGroup->setCountryId('8f241f11095410f38.37165361');
        $oGroup->setName('New Group Name');
        $oGroup->setDescription('New description');
        $oGroup->setRate('20.60');

        $oGroup->save();

        $oExpectedGroup = oeVATTBECountryVATGroup::createInstance();
        $oExpectedGroup->load($sGroupId);

        $this->assertEquals($oGroup->getCountryId(), $oExpectedGroup->getCountryId());
        $this->assertEquals($oGroup->getName(), $oExpectedGroup->getName());
        $this->assertEquals($oGroup->getDescription(), $oExpectedGroup->getDescription());
        $this->assertEquals($oGroup->getRate(), $oExpectedGroup->getRate());
    }
}
