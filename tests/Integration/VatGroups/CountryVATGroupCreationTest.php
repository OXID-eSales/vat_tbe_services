<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\VatGroups;

use OxidEsales\Eshop\Core\DisplayError;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Controller\Admin\CountryVatGroups;
use OxidEsales\EVatModule\Model\CountryVATGroup;
use OxidEsales\EVatModule\Model\CountryVATGroupsList;
use OxidEsales\EVatModule\Model\DbGateway\CountryVATGroupsDbGateway;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Testing CountryVatGroups class.
 */
class CountryVATGroupCreationTest extends BaseTestCase
{
    /**
     * Return different variants of country VAT group data to save.
     *
     * @return array
     */
    public static function providerCreateNewGroup()
    {
        return array(
            array('small VAT', 5, 'some description', '5.00'),
            array('small VAT', 5, '', '5.00'),
            array('small VAT', 5.5, 'some description', '5.50'),
            array('small VAT', 0, '', '0.00'),
            array('small VAT', 'five', 'some description', '0.00'),
            array('small VAT', '5.5', 'some description', '5.50'),
            array('small VAT', '5,5', 'some description', '5.00'),
            array('small VAT', '', 'some description', '0.00'),
        );
    }

    /**
     * Test if new group creation works.
     *
     * @param string $sGroupName        group name.
     * @param float  $fVATRate          vat rate.
     * @param string $sGroupDescription group description.
     * @param string $sExpectedVatRate  vat rate after database formatting.
     *
     * @dataProvider providerCreateNewGroup
     */
    public function testCreateNewGroup($sGroupName, $fVATRate, $sGroupDescription, $sExpectedVatRate)
    {
        $sCountryId = 'some_country_id';

        $_POST['editval'] = [
            'oxcountry__oxid'      => $sCountryId,
            'oevattbe_name'        => $sGroupName,
            'oevattbe_rate'        => $fVATRate,
            'oevattbe_description' => $sGroupDescription
        ];

        /** @var CountryVatGroups $oVATTBECountryVatGroups */
        $oVATTBECountryVatGroups = oxNew(CountryVatGroups::class);
        $oVATTBECountryVatGroups->addCountryVATGroup();
        $oVATTBECountryVatGroups->addCountryVATGroup();

        /** @var CountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew(CountryVATGroupsDbGateway::class);
        /** @var CountryVATGroupsList $oeVATTBECountryVATGroupsList */
        $oVATTBECountryVATGroupsList = oxNew(CountryVATGroupsList::class, $oGateway);
        $aVATTBECountryVATGroupsList = $oVATTBECountryVATGroupsList->load('some_country_id');

        $this->assertTrue(isset($aVATTBECountryVATGroupsList[0]), 'Newly created group must be in 0 position.');
        $this->assertFalse(isset($aVATTBECountryVATGroupsList[1]), 'Newly created group must not have duplication.');

        /** @var CountryVATGroup $oNewlyCreatedCountryVATGroup */
        $oNewlyCreatedCountryVATGroup = $aVATTBECountryVATGroupsList[0];

        $this->assertSame($sCountryId, $oNewlyCreatedCountryVATGroup->getCountryId());
        $this->assertSame($sGroupName, $oNewlyCreatedCountryVATGroup->getName());
        $this->assertSame($sExpectedVatRate, $oNewlyCreatedCountryVATGroup->getRate());
        $this->assertSame($sGroupDescription, $oNewlyCreatedCountryVATGroup->getDescription());
    }

    /**
     * Return data for group request with some required parameters missing.
     *
     * @return array
     */
    public static function providerCreateNewGroupFailWhenMissingRequiredData()
    {
        return array(
            array('', '5', 'some description'),
            array('', '', 'some description'),
        );
    }

    /**
     * Test if no new entry created when missing some required data.
     *
     * @param string $sGroupName        group name.
     * @param float  $fVATRate          vat rate.
     * @param string $sGroupDescription group description.
     *
     * @dataProvider providerCreateNewGroupFailWhenMissingRequiredData
     */
    public function testCreateNewGroupFailWithErrorMessageWhenMissingRequiredData($sGroupName, $fVATRate, $sGroupDescription)
    {
        $_POST['editval'] = [
            'oxcountry__oxid'      => 'some_country_id',
            'oevattbe_name'        => '',
            'oevattbe_rate'        => $fVATRate,
            'oevattbe_description' => $sGroupDescription
        ];

        /** @var CountryVatGroups $oVATTBECountryVatGroups */
        $oVATTBECountryVatGroups = oxNew(CountryVatGroups::class);
        $oVATTBECountryVatGroups->addCountryVATGroup();
        $oVATTBECountryVatGroups->addCountryVATGroup();

        /** @var CountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew(CountryVATGroupsDbGateway::class);
        /** @var CountryVATGroupsList $oeVATTBECountryVATGroupsList */
        $oVATTBECountryVATGroupsList = oxNew(CountryVATGroupsList::class, $oGateway);
        $aVATTBECountryVATGroupsList = $oVATTBECountryVATGroupsList->load('some_country_id');

        if (isset($aVATTBECountryVATGroupsList[0])) {
            $this->assertTrue(
                !isset($aVATTBECountryVATGroupsList[0]),
                'Some data missing so no new entry should be created. However got this: ' . serialize($aVATTBECountryVATGroupsList[0])
            );
        }

        $aEx = Registry::getSession()->getVariable('Errors');
        $this->assertTrue(isset($aEx['default'][0]), 'Error message must be set as some parameters missing.');

        /** @var DisplayError $oError */
        $oError = unserialize($aEx['default'][0]);

        $oLang = Registry::getLang();
        $sErrorMessage = $oLang->translateString('OEVATTBE_NEW_COUNTRY_VAT_GROUP_PARAMETER_MISSING');

        $this->assertSame($sErrorMessage, $oError->getOxMessage());
    }
}
