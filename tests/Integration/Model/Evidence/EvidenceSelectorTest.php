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
use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

/**
 * Test class for EvidenceCalculator.
 */
class EvidenceSelectorTest extends IntegrationTestCase
{
    public static function providerGetCountryWhenBothEvidenceDoNotMatch(): array
    {
        return [
            ['billing_address', 0],
            ['geo_location', 1]
        ];
    }

    /**
     * @param string  $sDefaultEvidence
     * @param int     $sExpectedEvidence
     */
    #[DataProvider('providerGetCountryWhenBothEvidenceDoNotMatch')]
    public function testGetCountryWhenBothEvidenceDoNotMatchDefaultTaken($sDefaultEvidence, $sExpectedEvidence)
    {
        $oConfig = Registry::getConfig();
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveDefaultEvidence($sDefaultEvidence);

        $oBillingEvidence = $this->createEvidence('billing_address', 'Germany');
        $oGeoLocationEvidence = $this->createEvidence('geo_location', 'Lithuania');
        $oEvidenceList = new EvidenceList($sExpectedEvidences = [$oBillingEvidence, $oGeoLocationEvidence]);
        $evidenceCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettings);

        $evidenceSelector = $this->getMockBuilder(EvidenceSelector::class)
            ->setConstructorArgs([
                $moduleSettings,
                $evidenceCollector
            ])
            ->onlyMethods(['getEvidenceList'])
            ->getMock();
        $evidenceSelector->method('getEvidenceList')->willReturn($oEvidenceList);

        $this->assertSame($sExpectedEvidences[$sExpectedEvidence], $evidenceSelector->getEvidence());
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
        $oEvidence->expects($this->any())->method('getId')->willReturn($sName);
        $oEvidence->expects($this->any())->method('getCountryId')->willReturn($sCountry);

        return $oEvidence;
    }

    /**
     * Creates evidence object with given name and country.
     */
    protected static function createEvidence2($sName, $sCountry)
    {
        $testCase = new class extends IntegrationTestCase {
            public function __construct() {}
        };

        $mock = $testCase
            ->getMockBuilder(Evidence::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->method('getId')->willReturn($sName);
        $mock->method('getCountryId')->willReturn($sCountry);

        return $mock;
    }
}
