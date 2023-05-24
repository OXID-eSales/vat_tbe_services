<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\VatGroups;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EVatModule\Controller\Admin\CountryVatGroups;
use OxidEsales\EVatModule\Model\CategoryVATGroupsList;
use OxidEsales\EVatModule\Model\CountryVATGroup;
use OxidEsales\EVatModule\Shop\Country;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Testing if Country has correct information about groups.
 */
class CountryInformationAboutVatGroupsTest extends BaseTestCase
{
    use ContainerTrait;

    /**
     * Test if country information updated when adding group.
     */
    public function testAddCountryVatGroup()
    {
        $sCountryId = '8f241f11095410f38.37165361';

        /** @var Country $oCountry */
        $oCountry = oxNew(Country::class);
        $oCountry->load($sCountryId);
        $this->assertFalse($oCountry->isOEVATTBEAtLeastOneGroupConfigured(), 'Country should not be marked as configured before test.');

        $oGroup = $this->get(CountryVATGroup::class);

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
        $sCountryId = 'a7c40f632a0804ab5.18804076';

        /** @var Country $oCountry */
        $oCountry = oxNew(Country::class);
        $oCountry->load($sCountryId);
        $this->assertTrue($oCountry->isOEVATTBEAtLeastOneGroupConfigured(), 'Country should be marked as configured before test.');

        /** @var CountryVatGroups $oVATTBECountryVatGroups */
        $oVATTBECountryVatGroups = oxNew(CountryVatGroups::class);
        $oVATTBECountryVatGroups->setEditObjectId($sCountryId);

        $_POST['countryVATGroupId'] = '79';
        $oVATTBECountryVatGroups->deleteCountryVatGroup();
        $oCountry->load($sCountryId);
        $this->assertTrue($oCountry->isOEVATTBEAtLeastOneGroupConfigured(), 'Country should be still marked as configured as one more group left.');

        $_POST['countryVATGroupId'] = '80';
        $oVATTBECountryVatGroups->deleteCountryVatGroup();
        $oCountry->load($sCountryId);
        $this->assertFalse($oCountry->isOEVATTBEAtLeastOneGroupConfigured(), 'Country should be marked as not configured as no more groups left.');
    }
}
