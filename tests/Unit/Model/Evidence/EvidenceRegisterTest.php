<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model\Evidence;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\Evidence\EvidenceRegister;
use OxidEsales\EVatModule\Model\Evidence\Item\BillingCountryEvidence;
use PHPUnit\Framework\TestCase;

/**
 * Test class for EvidenceRegister.
 *
 * @covers EvidenceRegister
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
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', []);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', []);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class, $oConfig);
        $oRegister->registerEvidence('oeVATTBEBillingCountryEvidence');

        $this->assertEquals(['oeVATTBEBillingCountryEvidence'], $oRegister->getRegisteredEvidences());

        return $oRegister;
    }

    /**
     * No evidences are registered;
     * New evidence is passed for registration with default activation value;
     * Evidence should be added to active evidences list, but be inactive.
     *
     * @param EvidenceRegister $oRegister
     *
     * @depends testRegisteringEvidenceWhenNoEvidencesRegistered
     */
    public function testActivatingEvidenceAfterSuccessfulRegistration($oRegister)
    {
        $this->assertEquals(['billing_country' => 0], $oRegister->getActiveEvidences());
    }

    /**
     * Default evidences are registered;
     * New evidence is passed for registration;
     * Evidence should get registered without removing default evidences.
     */
    public function testRegisteringEvidenceWhenDefaultEvidencesRegistered()
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', []);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', ['oeDefaultEvidence1', 'oeDefaultEvidence2']);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class, $oConfig);
        $oRegister->registerEvidence('oeVATTBEBillingCountryEvidence');

        $aExpectedEvidences = ['oeDefaultEvidence1', 'oeDefaultEvidence2', 'oeVATTBEBillingCountryEvidence'];
        $this->assertEquals($aExpectedEvidences, $oRegister->getRegisteredEvidences());
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
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', ['billing_country' => 1]);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', ['oeVATTBEBillingCountryEvidence']);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class, $oConfig);
        $oRegister->unregisterEvidence('oeVATTBEBillingCountryEvidence');

        $this->assertEquals([], $oRegister->getRegisteredEvidences());

        return $oRegister;
    }

    /**
     * Registered evidences exists;
     * Evidence class is passed for unregistering;
     * Evidence should be removed from active evidences list.
     *
     * @param EvidenceRegister $oRegister
     *
     * @depends testUnregisteringEvidenceWhenItIsRegistered
     */
    public function testRemovingEvidenceAfterItIsUnregistered($oRegister)
    {
        $this->assertEquals([], $oRegister->getActiveEvidences());
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
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', ['billing_country' => 1, 'geo_location' => 1]);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', ['oeVATTBEBillingCountryEvidence', 'GeoClass']);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class, $oConfig);
        $oRegister->unregisterEvidence('oeVATTBEBillingCountryEvidence');

        $this->assertEquals([1 => 'GeoClass'], $oRegister->getRegisteredEvidences());

        return $oRegister;
    }

    /**
     * Registered evidences exists;
     * More evidences exist in the list;
     * Evidence class is passed for unregistering;
     * Evidence should be removed from the list but other evidences should still exist.
     *
     * @param EvidenceRegister $oRegister
     *
     * @depends testUnregisteringEvidenceWhenItIsRegisteredAndMoreEvidencesExist
     */
    public function testRemovingEvidenceAfterItIsUnregisteredAndMoreEvidencesExist($oRegister)
    {
        $this->assertEquals(['geo_location' => 1], $oRegister->getActiveEvidences());
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
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', ['billing_country' => 1]);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', ['oeVATTBEBillingCountryEvidence']);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class, $oConfig);
        $oRegister->unregisterEvidence('SomeNonExistingEvidenceClass');

        $this->assertEquals(['oeVATTBEBillingCountryEvidence'], $oRegister->getRegisteredEvidences());

        return $oRegister;
    }

    /**
     * Registered evidences exists;
     * More evidences exist in the list;
     * Evidence class is passed for unregistering;
     * Evidence should be removed from the list but other evidences should still exist.
     *
     * @param EvidenceRegister $oRegister
     *
     * @depends testUnregisteringEvidenceWhenEvidenceIsNotRegistered
     */
    public function testRemovingEvidenceWhenEvidenceIsNotRegistered($oRegister)
    {
        $this->assertEquals(['billing_country' => 1], $oRegister->getActiveEvidences());
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
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', ['billing_country' => 1, 'geo_location' => 1]);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', ['oeVATTBEBillingCountryEvidence', 'GeoClass']);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class, $oConfig);
        $oRegister->unregisterEvidence(BillingCountryEvidence::class);

        $this->assertEquals([1 => 'GeoClass'], $oRegister->getRegisteredEvidences());

        return $oRegister;
    }

    /**
     * Inactive evidence exist;
     * Inactive evidence id is passed;
     * Evidence should be activated.
     */
    public function testActivatingEvidenceWhenItIsRegistered()
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', ['InactiveEvidenceId' => 0]);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class, $oConfig);
        $oRegister->activateEvidence('InactiveEvidenceId');

        $this->assertEquals(['InactiveEvidenceId' => 1], $oRegister->getActiveEvidences());
    }

    /**
     * Active evidence exist;
     * Active evidence id is passed;
     * Evidence should be deactivated.
     */
    public function testDeactivatingEvidenceWhenItIsRegistered()
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', ['InactiveEvidenceId' => 1]);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class, $oConfig);
        $oRegister->deactivateEvidence('InactiveEvidenceId');

        $this->assertEquals(['InactiveEvidenceId' => 0], $oRegister->getActiveEvidences());
    }

    /**
     * No evidences are registered;
     * Non existing evidence id is passed;
     * Nothing should be changed.
     */
    public function testDeactivatingEvidenceWhenItIsNotRegistered()
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', []);

        /** @var EvidenceRegister $oCollector */
        $oRegister = oxNew(EvidenceRegister::class, $oConfig);
        $oRegister->activateEvidence('NonExistingEvidenceId');

        $this->assertEquals([], $oRegister->getActiveEvidences());
    }
}
