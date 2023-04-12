<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model\Evidence;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\Evidence\EvidenceList;
use OxidEsales\EVatModule\Model\Evidence\EvidenceSelector;
use OxidEsales\EVatModule\Model\Evidence\Item\Evidence;
use OxidEsales\EVatModule\Service\ModuleSettings;
use PHPUnit\Framework\TestCase;

/**
 * Test class for EvidenceCalculator.
 *
 * @covers EvidenceSelector
 */
class EvidenceSelectorTest extends TestCase
{
    public function providerGetCountryWhenBothEvidenceDoNotMatch(): array
    {
        $oBillingEvidence = $this->_createEvidence('billing_address', 'Germany');
        $oGeoLocationEvidence = $this->_createEvidence('geo_location', 'Lithuania');
        $oEvidenceList = new EvidenceList([$oBillingEvidence, $oGeoLocationEvidence]);

        return [
            [$oEvidenceList, 'billing_address', $oBillingEvidence],
            [$oEvidenceList, 'geo_location', $oGeoLocationEvidence]
        ];
    }

    /**
     * @param EvidenceList $oEvidenceList
     * @param string       $sDefaultEvidence
     * @param Evidence     $sExpectedEvidence
     *
     * @dataProvider providerGetCountryWhenBothEvidenceDoNotMatch
     */
    public function testGetCountryWhenBothEvidenceDoNotMatchDefaultTaken($oEvidenceList, $sDefaultEvidence, $sExpectedEvidence)
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', $sDefaultEvidence);

//        $oCalculator = new EvidenceSelector($oEvidenceList, $oConfig);

        //TODO: pass module setting and EvidenceCollect
        $moduleSettingsMock = $this->createMock(ModuleSettings::class);
        $oCalculator = new EvidenceSelector($moduleSettingsMock, $oEvidenceList);

        $this->assertSame($sExpectedEvidence, $oCalculator->getEvidence());
    }

    public function testGetCountryWhenDefaultEvidenceEmpty()
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'default_evidence');

        $oBillingEvidence = $this->_createEvidence('billing_address', 'Germany');
        $oGeoLocationEvidence = $this->_createEvidence('geo_location', 'Lithuania');
        $oDefaultEvidence = $this->_createEvidence('default_evidence', '');
        $oEvidenceList = new EvidenceList([$oBillingEvidence, $oGeoLocationEvidence, $oDefaultEvidence]);

//        $oCalculator = new EvidenceSelector($oEvidenceList, $oConfig);

        $moduleSettingsMock = $this->createMock(ModuleSettings::class);
        $oCalculator = new EvidenceSelector($moduleSettingsMock, $oEvidenceList);

        $this->assertSame($oBillingEvidence, $oCalculator->getEvidence());
    }

    public function testGetCountryWhenDefaultAndFirstEvidenceEmpty()
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'default_evidence');

        $oBillingEvidence = $this->_createEvidence('billing_address', '');
        $oGeoLocationEvidence = $this->_createEvidence('geo_location', 'Lithuania');
        $oDefaultEvidence = $this->_createEvidence('default_evidence', '');
        $oEvidenceList = new EvidenceList([$oBillingEvidence, $oGeoLocationEvidence, $oDefaultEvidence]);

//        $oCalculator = new EvidenceSelector($oEvidenceList, $oConfig);

        $moduleSettingsMock = $this->createMock(ModuleSettings::class);
        $oCalculator = new EvidenceSelector($moduleSettingsMock, $oEvidenceList);

        $this->assertSame($oGeoLocationEvidence, $oCalculator->getEvidence());
    }

    public function testGetCountryWithEmptyList()
    {
        $oConfig = Registry::getConfig();
        $oEvidenceList = new EvidenceList();
//        $oCalculator = new EvidenceSelector($oEvidenceList, $oConfig);

        $moduleSettingsMock = $this->createMock(ModuleSettings::class);
        $oCalculator = new EvidenceSelector($moduleSettingsMock, $oEvidenceList);

        $this->assertSame(null, $oCalculator->getEvidence());
    }

    public function testIsEvidencesContradictingWhenEvidencesDoNotMatch()
    {
        $oBillingEvidence = $this->_createEvidence('billing_address', 'Germany');
        $oGeoLocationEvidence = $this->_createEvidence('geo_location', 'Germany');
        $oEvidenceList = new EvidenceList([$oBillingEvidence, $oGeoLocationEvidence]);

//        $oCalculator = new EvidenceSelector($oEvidenceList, Registry::getConfig());

        $moduleSettingsMock = $this->createMock(ModuleSettings::class);
        $oCalculator = new EvidenceSelector($moduleSettingsMock, $oEvidenceList);

        $this->assertSame(false, $oCalculator->isEvidencesContradicting());
    }

    public function testIsEvidencesContradictingWhenEvidencesMatch()
    {
        $oBillingEvidence = $this->_createEvidence('billing_address', 'Germany');
        $oGeoLocationEvidence = $this->_createEvidence('geo_location', 'Lithuania');
        $oEvidenceList = new EvidenceList([$oBillingEvidence, $oGeoLocationEvidence]);

//        $oCalculator = new EvidenceSelector($oEvidenceList, Registry::getConfig());

        $moduleSettingsMock = $this->createMock(ModuleSettings::class);
        $oCalculator = new EvidenceSelector($moduleSettingsMock, $oEvidenceList);

        $this->assertSame(true, $oCalculator->isEvidencesContradicting());
    }

    /**
     * Creates evidence object with given name and country.
     *
     * @param string $sName
     * @param string $sCountry
     *
     * @return Evidence
     */
    protected function _createEvidence($sName, $sCountry)
    {
        $oEvidence = $this->createMock(Evidence::class);
        $oEvidence->expects($this->any())->method('getId')->will($this->returnValue($sName));
        $oEvidence->expects($this->any())->method('getCountryId')->will($this->returnValue($sCountry));

        return $oEvidence;
    }
}
