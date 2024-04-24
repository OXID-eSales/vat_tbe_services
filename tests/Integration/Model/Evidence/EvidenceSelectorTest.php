<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Model\Evidence;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EVatModule\Model\Evidence\EvidenceCollector;
use OxidEsales\EVatModule\Model\Evidence\EvidenceList;
use OxidEsales\EVatModule\Model\Evidence\EvidenceSelector;
use OxidEsales\EVatModule\Model\Evidence\Item\Evidence;
use OxidEsales\EVatModule\Service\ModuleSettings;
use PHPUnit\Framework\TestCase;

/**
 * Test class for EvidenceCalculator.
 */
class EvidenceSelectorTest extends TestCase
{
    public static function providerGetCountryWhenBothEvidenceDoNotMatch(): array
    {
        $evidenceSelector = new self('EvidenceSelectorTest');
        $oBillingEvidence = $evidenceSelector->createEvidence('billing_address', 'Germany');
        $oGeoLocationEvidence = $evidenceSelector->createEvidence('geo_location', 'Lithuania');
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
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveDefaultEvidence($sDefaultEvidence);

        $evidenceCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettings);

        $evidenceSelector = $this->getMockBuilder(EvidenceSelector::class)
            ->setConstructorArgs([
                $moduleSettings,
                $evidenceCollector
            ])
            ->onlyMethods(['getEvidenceList'])
            ->getMock();
        $evidenceSelector->method('getEvidenceList')->willReturn($oEvidenceList);

        $this->assertSame($sExpectedEvidence, $evidenceSelector->getEvidence());
    }

    public function testGetCountryWhenDefaultEvidenceEmpty()
    {
        $oConfig = Registry::getConfig();
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveDefaultEvidence('default_evidence');

        $oBillingEvidence = $this->createEvidence('billing_address', 'Germany');
        $oGeoLocationEvidence = $this->createEvidence('geo_location', 'Lithuania');
        $oDefaultEvidence = $this->createEvidence('default_evidence', '');
        $oEvidenceList = new EvidenceList([$oBillingEvidence, $oGeoLocationEvidence, $oDefaultEvidence]);

        $evidenceCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettings);

        $evidenceSelector = $this->getMockBuilder(EvidenceSelector::class)
            ->setConstructorArgs([
                $moduleSettings,
                $evidenceCollector
            ])
            ->onlyMethods(['getEvidenceList'])
            ->getMock();
        $evidenceSelector->method('getEvidenceList')->willReturn($oEvidenceList);

        $this->assertSame($oBillingEvidence, $evidenceSelector->getEvidence());
    }

    public function testGetCountryWhenDefaultAndFirstEvidenceEmpty()
    {
        $oConfig = Registry::getConfig();
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveDefaultEvidence('default_evidence');

        $oBillingEvidence = $this->createEvidence('billing_address', '');
        $oGeoLocationEvidence = $this->createEvidence('geo_location', 'Lithuania');
        $oDefaultEvidence = $this->createEvidence('default_evidence', '');
        $oEvidenceList = new EvidenceList([$oBillingEvidence, $oGeoLocationEvidence, $oDefaultEvidence]);

        $evidenceCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettings);

        $evidenceSelector = $this->getMockBuilder(EvidenceSelector::class)
            ->setConstructorArgs([
                $moduleSettings,
                $evidenceCollector
            ])
            ->onlyMethods(['getEvidenceList'])
            ->getMock();
        $evidenceSelector->method('getEvidenceList')->willReturn($oEvidenceList);

        $this->assertSame($oGeoLocationEvidence, $evidenceSelector->getEvidence());
    }

    public function testGetCountryWithEmptyList()
    {
        $oConfig = Registry::getConfig();
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);

        $oEvidenceList = new EvidenceList();

        $evidenceCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettings);

        $evidenceSelector = $this->getMockBuilder(EvidenceSelector::class)
            ->setConstructorArgs([
                $moduleSettings,
                $evidenceCollector
            ])
            ->onlyMethods(['getEvidenceList'])
            ->getMock();
        $evidenceSelector->method('getEvidenceList')->willReturn($oEvidenceList);

        $this->assertSame(null, $evidenceSelector->getEvidence());
    }

    public function testIsEvidencesContradictingWhenEvidencesDoNotMatch()
    {
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);

        $oBillingEvidence = $this->createEvidence('billing_address', 'Germany');
        $oGeoLocationEvidence = $this->createEvidence('geo_location', 'Germany');
        $oEvidenceList = new EvidenceList([$oBillingEvidence, $oGeoLocationEvidence]);

        $evidenceCollector = oxNew(EvidenceCollector::class, Registry::getConfig(), $moduleSettings);

        $evidenceSelector = $this->getMockBuilder(EvidenceSelector::class)
            ->setConstructorArgs([
                $moduleSettings,
                $evidenceCollector
            ])
            ->onlyMethods(['getEvidenceList'])
            ->getMock();
        $evidenceSelector->method('getEvidenceList')->willReturn($oEvidenceList);

        $this->assertSame(false, $evidenceSelector->isEvidencesContradicting());
    }

    public function testIsEvidencesContradictingWhenEvidencesMatch()
    {
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);

        $oBillingEvidence = $this->createEvidence('billing_address', 'Germany');
        $oGeoLocationEvidence = $this->createEvidence('geo_location', 'Lithuania');
        $oEvidenceList = new EvidenceList([$oBillingEvidence, $oGeoLocationEvidence]);

        $evidenceCollector = oxNew(EvidenceCollector::class, Registry::getConfig(), $moduleSettings);

        $evidenceSelector = $this->getMockBuilder(EvidenceSelector::class)
            ->setConstructorArgs([
                $moduleSettings,
                $evidenceCollector
            ])
            ->onlyMethods(['getEvidenceList'])
            ->getMock();
        $evidenceSelector->method('getEvidenceList')->willReturn($oEvidenceList);

        $this->assertSame(true, $evidenceSelector->isEvidencesContradicting());
    }

    /**
     * Creates evidence object with given name and country.
     */
    protected function createEvidence($sName, $sCountry)
    {
        $oEvidence = $this->createMock(Evidence::class);
        $oEvidence->expects($this->any())->method('getId')->will($this->returnValue($sName));
        $oEvidence->expects($this->any())->method('getCountryId')->will($this->returnValue($sCountry));

        return $oEvidence;
    }
}
