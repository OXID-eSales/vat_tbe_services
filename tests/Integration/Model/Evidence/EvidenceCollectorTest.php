<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Model\Evidence;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EVatModule\Model\Evidence\EvidenceCollector;
use OxidEsales\EVatModule\Model\Evidence\EvidenceList;
use OxidEsales\EVatModule\Model\Evidence\Item\BillingCountryEvidence;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Traits\ServiceContainer;
use PHPUnit\Framework\TestCase;

/**
 * Test class for oeVATTBEEvidenceCalculator.
 */
class EvidenceCollectorTest extends TestCase
{
    use ServiceContainer;

    /**
     * Evidence is registered;
     * Evidence is set to be active;
     * Evidence should appear in evidence list.
     */
    public function testGetEvidencesWhenEvidencesExistAndIsActive()
    {
        //TODO: tmp solution, fix after moving to integration test
        ContainerFactory::resetContainer();

        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', ['billing_country' => 1]);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', ['oeVATTBEBillingCountryEvidence']);
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveCountryEvidences(['billing_country' => 1]);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class]);

        $oExpectedEvidence = oxNew(BillingCountryEvidence::class, Registry::getSession());
        $oEvidenceList = oxNew(EvidenceList::class);
        $oEvidenceList->add($oExpectedEvidence);

        $oCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettings);

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
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveCountryEvidences(['billing_country' => 0]);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class]);

        $oEvidenceList = oxNew(EvidenceList::class);

        $oCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettings);
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
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveCountryEvidences([]);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class]);

        $oCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettings);
        $oCollector->getEvidenceList();

        $this->assertEquals(['billing_country' => 0], $moduleSettings->getCountryEvidences());
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
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveCountryEvidences([]);
        $moduleSettings->saveEvidenceClasses([]);

        $oEvidenceList = oxNew(EvidenceList::class);

        $oCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettings);
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
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveCountryEvidences(['non_existing_id' => 1]);
        $moduleSettings->saveEvidenceClasses([]);

        $oCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettings);
        $oCollector->getEvidenceList();

        $this->assertEquals([], $moduleSettings->getCountryEvidences());
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
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveCountryEvidences(['non_existing_id' => 1]);
        $moduleSettings->saveEvidenceClasses(['NonExistingEvidenceClass']);

        $oEvidenceList = oxNew(EvidenceList::class);

        $oCollector = oxNew(EvidenceCollector::class, $oConfig, $moduleSettings);
        $this->assertEquals($oEvidenceList, $oCollector->getEvidenceList());
    }
}
