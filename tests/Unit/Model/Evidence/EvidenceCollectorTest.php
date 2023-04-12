<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model\Evidence;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\Evidence\EvidenceCollector;
use OxidEsales\EVatModule\Model\Evidence\EvidenceList;
use OxidEsales\EVatModule\Model\Evidence\Item\BillingCountryEvidence;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Shop\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for oeVATTBEEvidenceCalculator.
 *
 * @covers EvidenceCollector
 */
class EvidenceCollectorTest extends TestCase
{
    /**
     * Evidence is registered;
     * Evidence is set to be active;
     * Evidence should appear in evidence list.
     */
    public function testGetEvidencesWhenEvidencesExistAndIsActive()
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', ['billing_country' => 1]);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', ['oeVATTBEBillingCountryEvidence']);

        /** @var User|MockObject $oUser */
        $oUser = $this->createMock(User::class);

        //TODO: set user in session if necessary

        $oExpectedEvidence = oxNew(BillingCountryEvidence::class, Registry::getSession());
        $oEvidenceList = oxNew(EvidenceList::class);
        $oEvidenceList->add($oExpectedEvidence);

//        $oCollector = oxNew(EvidenceCollector::class, $oUser, $oConfig);
        $moduleSettingsMock = $this->createMock(ModuleSettings::class);

        $oCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettingsMock);
        $this->assertEquals($oEvidenceList, $oCollector->getEvidenceList());
    }

    /**
     * Evidence is registered;
     * Evidence is set to be not active;
     * Empty evidence list should be returned.
     */
    public function testGetEvidencesWhenEvidenceExistsButIsNotActive()
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', ['billing_country' => 0]);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', ['oeVATTBEBillingCountryEvidence']);

        /** @var User|MockObject $oUser */
        $oUser = $this->createMock(User::class);

        $oEvidenceList = oxNew(EvidenceList::class);

//        $oCollector = oxNew(EvidenceCollector::class, $oUser, $oConfig);

        $moduleSettingsMock = $this->createMock(ModuleSettings::class);
        $oCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettingsMock);
        $this->assertEquals($oEvidenceList, $oCollector->getEvidenceList());
    }

    /**
     * Evidence is registered;
     * Active evidence list is empty;
     * Registered evidence should be put to active evidences list and set to be not active.
     */
    public function testGetEvidencesWhenEvidenceIsRegisteredButActiveEvidencesListIsEmpty()
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', []);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', ['oeVATTBEBillingCountryEvidence']);

        /** @var User|MockObject $oUser */
        $oUser = $this->createMock(User::class);
//        $oCollector = oxNew(EvidenceCollector::class, $oUser, $oConfig);

        $moduleSettingsMock = $this->createMock(ModuleSettings::class);
        $oCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettingsMock);
        $oCollector->getEvidenceList();

        $this->assertEquals(['billing_country' => 0], $oConfig->getConfigParam('aOeVATTBECountryEvidences'));
    }

    /**
     * No evidences are registered;
     * No evidences are set to be active;
     * Empty evidence list should be returned.
     */
    public function testGetEvidencesWhenNoEvidencesAreSet()
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', []);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', []);

        /** @var User|MockObject $oUser */
        $oUser = $this->createMock(User::class);

        $oEvidenceList = oxNew(EvidenceList::class);

//        $oCollector = oxNew(EvidenceCollector::class, $oUser, $oConfig);

        $moduleSettingsMock = $this->createMock(ModuleSettings::class);
        $oCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettingsMock);
        $this->assertEquals($oEvidenceList, $oCollector->getEvidenceList());
    }

    /**
     * No evidences are registered;
     * Active evidence list contains non registered evidence;
     * Evidence should be removed from active evidences list.
     */
    public function testGetEvidencesWhenNoEvidenceIsRegisteredButActiveEvidenceListIsNotEmpty()
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', ['non_existing_id' => 1]);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', []);

        /** @var User|MockObject $oUser */
        $oUser = $this->createMock(User::class);
//        $oCollector = oxNew(EvidenceCollector::class, $oUser, $oConfig);

        $moduleSettingsMock = $this->createMock(ModuleSettings::class);
        $oCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettingsMock);
        $oCollector->getEvidenceList();

        $this->assertEquals([], $oConfig->getConfigParam('aOeVATTBECountryEvidences'));
    }

    /**
     * Non existing evidence is registered;
     * Non existing evidence is set to be active;
     * Empty evidence list should be returned.
     */
    public function testGetEvidencesWhenEvidencesDoesNotExists()
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', ['non_existing_id' => 1]);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', ['NonExistingEvidenceClass']);

        /** @var User|MockObject $oUser */
        $oUser = $this->createMock(User::class);

        $oEvidenceList = oxNew(EvidenceList::class);

//        $oCollector = oxNew(EvidenceCollector::class, $oUser, $oConfig);

        $moduleSettingsMock = $this->createMock(ModuleSettings::class);
        $oCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettingsMock);
        $this->assertEquals($oEvidenceList, $oCollector->getEvidenceList());
    }
}
