<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Testing if Country has correct information about groups.
 *
 * @covers oeVATTBECountryVatGroups
 * @covers oeVATTBECountryVATGroupsDbGateway
 * @covers oeVATTBECountryVATGroup
 * @covers oeVATTBECountryVATGroupsList
 * @covers oeVATTBEOxCountry
 */
class Integration_oeVatTbe_VATGroups_oeVATTBECountryInformationAboutVatGroupsTest extends OxidTestCase
{
    /**
     * Test if country information updated when adding group.
     */
    public function testAddCountryVatGroup()
    {
        $this->setTablesForCleanup('oevattbe_countryvatgroups');

        $sCountryId = '8f241f11095410f38.37165361';

        /** @var oxCountry|oeVATTBEOxCountry $oCountry */
        $oCountry = oxNew('oxCountry');
        $oCountry->load($sCountryId);
        $this->assertFalse($oCountry->isOEVATTBEAtLeastOneGroupConfigured(), 'Country should not be marked as configured before test.');

        $oGroup = oeVATTBECountryVATGroup::createInstance();

        $oGroup->setCountryId($sCountryId);
        $oGroup->setName('Group Name');
        $oGroup->setDescription('Some description');
        $oGroup->setRate('20.50');
        $oGroup->save();

        $oCountry->load($sCountryId);
        $this->assertTrue($oCountry->isOEVATTBEAtLeastOneGroupConfigured(), 'Country should be configured as new group was created.');
    }

    /**
     * Test if country information updated when deleting groups.
     */
    public function testDeleteCountryVatGroup()
    {
        $this->setTablesForCleanup('oevattbe_countryvatgroups');

        $sCountryId = 'a7c40f632a0804ab5.18804076';

        /** @var oxCountry|oeVATTBEOxCountry $oCountry */
        $oCountry = oxNew('oxCountry');
        $oCountry->load($sCountryId);
        $this->assertTrue($oCountry->isOEVATTBEAtLeastOneGroupConfigured(), 'Country should be marked as configured before test.');

        /** @var oeVATTBECountryVatGroups $oVATTBECountryVatGroups */
        $oVATTBECountryVatGroups = oxNew('oeVATTBECountryVatGroups');
        $oVATTBECountryVatGroups->setEditObjectId($sCountryId);

        $this->setRequestParameter('countryVATGroupId', '79');
        $oVATTBECountryVatGroups->deleteCountryVatGroup();
        $oCountry->load($sCountryId);
        $this->assertTrue($oCountry->isOEVATTBEAtLeastOneGroupConfigured(), 'Country should be still marked as configured as one more group left.');

        $this->setRequestParameter('countryVATGroupId', '80');
        $oVATTBECountryVatGroups->deleteCountryVatGroup();
        $oCountry->load($sCountryId);
        $this->assertFalse($oCountry->isOEVATTBEAtLeastOneGroupConfigured(), 'Country should be marked as not configured as no more groups left.');
    }
}
