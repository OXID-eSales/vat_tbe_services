<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Model\Evidence;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EVatModule\Model\Evidence\EvidenceRegister;
use OxidEsales\EVatModule\Model\Evidence\Item\BillingCountryEvidence;
use OxidEsales\EVatModule\Service\ModuleSettings;
use PHPUnit\Framework\TestCase;

/**
 * Test class for EvidenceRegister.
 */
class EvidenceRegisterTest extends TestCase
{
    /**
     * No evidences are registered;
     * New evidence is passed for registration;
     * Evidence should get registered.
     *
     * @return EvidenceRegister
     */
    public function testRegisteringEvidenceWhenNoEvidencesRegistered()
    {
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveCountryEvidences([]);
        $moduleSettings->saveEvidenceClasses([]);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class);
        $oRegister->registerEvidence(BillingCountryEvidence::class);

        $this->assertEquals([BillingCountryEvidence::class], $moduleSettings->getEvidenceClasses());

        return $oRegister;
    }

    /**
     * No evidences are registered;
     * New evidence is passed for registration with default activation value;
     * Evidence should be added to active evidences list, but be inactive.
     *
     * @depends testRegisteringEvidenceWhenNoEvidencesRegistered
     */
    public function testActivatingEvidenceAfterSuccessfulRegistration()
    {
        $this->assertEquals(['billing_country' => 0], ContainerFacade::get(ModuleSettings::class)->getCountryEvidences());
    }

    /**
     * Default evidences are registered;
     * New evidence is passed for registration;
     * Evidence should get registered without removing default evidences.
     */
    public function testRegisteringEvidenceWhenDefaultEvidencesRegistered()
    {
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveCountryEvidences([]);
        $moduleSettings->saveEvidenceClasses(['oeDefaultEvidence1', 'oeDefaultEvidence2']);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class);
        $oRegister->registerEvidence(BillingCountryEvidence::class);

        $aExpectedEvidences = ['oeDefaultEvidence1', 'oeDefaultEvidence2', BillingCountryEvidence::class];
        $this->assertEquals($aExpectedEvidences, $moduleSettings->getEvidenceClasses());
    }

    /**
     * Registered evidences exists;
     * Evidence class is passed for unregistering;
     * Evidence should get registered.
     *
     * @return EvidenceRegister
     */
    public function testUnregisteringEvidenceWhenItIsRegistered()
    {
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveCountryEvidences(['billing_country' => 1]);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class]);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class);
        $oRegister->unregisterEvidence(BillingCountryEvidence::class);

        $this->assertEquals([], $moduleSettings->getEvidenceClasses());

        return $oRegister;
    }

    /**
     * Registered evidences exists;
     * Evidence class is passed for unregistering;
     * Evidence should be removed from active evidences list.
     *
     * @depends testUnregisteringEvidenceWhenItIsRegistered
     */
    public function testRemovingEvidenceAfterItIsUnregistered()
    {
        $this->assertEquals([], ContainerFacade::get(ModuleSettings::class)->getCountryEvidences());
    }

    /**
     * Registered evidences exists;
     * More evidences exist in the list;
     * Evidence class is passed for unregistering;
     * Evidence should get unregistered but other evidences should still exist.
     *
     * @return EvidenceRegister
     */
    public function testUnregisteringEvidenceWhenItIsRegisteredAndMoreEvidencesExist()
    {
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveCountryEvidences(['billing_country' => 1, 'geo_location' => 1]);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class, 'GeoClass']);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class);
        $oRegister->unregisterEvidence(BillingCountryEvidence::class);

        $this->assertEquals([1 => 'GeoClass'], $moduleSettings->getEvidenceClasses());

        return $oRegister;
    }

    /**
     * Registered evidences exists;
     * More evidences exist in the list;
     * Evidence class is passed for unregistering;
     * Evidence should be removed from the list but other evidences should still exist.
     *
     * @depends testUnregisteringEvidenceWhenItIsRegisteredAndMoreEvidencesExist
     */
    public function testRemovingEvidenceAfterItIsUnregisteredAndMoreEvidencesExist()
    {
        $this->assertEquals(['geo_location' => 1], ContainerFacade::get(ModuleSettings::class)->getCountryEvidences());
    }

    /**
     * Evidence exist in the list;
     * Non existing evidence class is passed for unregistering;
     * Evidence list should stay intact.
     *
     * @return EvidenceRegister
     */
    public function testUnregisteringEvidenceWhenEvidenceIsNotRegistered()
    {
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveCountryEvidences(['billing_country' => 1]);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class]);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class);
        $oRegister->unregisterEvidence('SomeNonExistingEvidenceClass');

        $this->assertEquals([BillingCountryEvidence::class], $moduleSettings->getEvidenceClasses());

        return $oRegister;
    }

    /**
     * Registered evidences exists;
     * More evidences exist in the list;
     * Evidence class is passed for unregistering;
     * Evidence should be removed from the list but other evidences should still exist.
     *
     * @depends testUnregisteringEvidenceWhenEvidenceIsNotRegistered
     */
    public function testRemovingEvidenceWhenEvidenceIsNotRegistered()
    {
        $this->assertEquals(['billing_country' => 1], ContainerFacade::get(ModuleSettings::class)->getCountryEvidences());
    }

    /**
     * Registered evidences exists;
     * Evidence class is passed for unregistering;
     * Evidence should get registered.
     *
     * @return EvidenceRegister
     */
    public function testUnregisteringEvidenceWhenItIsNotRegistered()
    {
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveCountryEvidences(['billing_country' => 1, 'geo_location' => 1]);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class, 'GeoClass']);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class);
        $oRegister->unregisterEvidence(BillingCountryEvidence::class);

        $this->assertEquals([1 => 'GeoClass'], $moduleSettings->getEvidenceClasses());

        return $oRegister;
    }

    /**
     * Inactive evidence exist;
     * Inactive evidence id is passed;
     * Evidence should be activated.
     */
    public function testActivatingEvidenceWhenItIsRegistered()
    {
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveCountryEvidences(['InactiveEvidenceId' => 0]);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class);
        $oRegister->activateEvidence('InactiveEvidenceId');

        $this->assertEquals(['InactiveEvidenceId' => 1], $moduleSettings->getCountryEvidences());
    }

    /**
     * Active evidence exist;
     * Active evidence id is passed;
     * Evidence should be deactivated.
     */
    public function testDeactivatingEvidenceWhenItIsRegistered()
    {
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveCountryEvidences(['InactiveEvidenceId' => 1]);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class);
        $oRegister->deactivateEvidence('InactiveEvidenceId');

        $this->assertEquals(['InactiveEvidenceId' => 0], $moduleSettings->getCountryEvidences());
    }

    /**
     * No evidences are registered;
     * Non existing evidence id is passed;
     * Nothing should be changed.
     */
    public function testDeactivatingEvidenceWhenItIsNotRegistered()
    {
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveCountryEvidences([]);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class);
        $oRegister->activateEvidence('NonExistingEvidenceId');

        $this->assertEquals([], $moduleSettings->getCountryEvidences());
    }
}
