<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model\Evidence;

use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * Class for registering evidences.
 * After registration these evidences will be available
 */
class EvidenceRegister
{
    use ServiceContainer;

    /**
     * Registers evidence to config.
     * This evidence will be used when calculating user country.
     * By default registered evidence is made inactive,
     * but shop administrator can go to module configuration and activate it.
     *
     * @param string $evidenceClass Evidence class name. It should be reachable by shop (e.g. in activated module).
     * @param bool   $isActive      Whether to make this evidence active after registration. Default is false.
     */
    public function registerEvidence(string $evidenceClass, bool $isActive = false): void
    {
        if (!class_exists($evidenceClass)) {
            return;
        }

        $evidenceClasses = $this->getServiceFromContainer(ModuleSettings::class)->getEvidenceClasses();

        if (!in_array($evidenceClass, $evidenceClasses)) {
            $evidenceClasses[] = $evidenceClass;
            $this->getServiceFromContainer(ModuleSettings::class)->saveEvidenceClasses($evidenceClasses);
        }

        $this->addEvidenceToEvidenceList($evidenceClass, $isActive);
    }

    /**
     * Registers evidence to config.
     * This evidence will be used when calculating user country.
     * By default registered evidence is made inactive,
     * but shop administrator can go to module configuration and activate it.
     *
     * @param string $evidenceClass Evidence class name. It should be reachable by shop (e.g. in activated module).
     */
    public function unregisterEvidence(string $evidenceClass): void
    {
        if (!class_exists($evidenceClass)) {
            return;
        }

        $evidenceClasses = $this->getServiceFromContainer(ModuleSettings::class)->getEvidenceClasses();

        // Check whether this class was registered in first place
        if (($key = array_search($evidenceClass, $evidenceClasses)) !== false) {
            unset($evidenceClasses[$key]);
            $this->getServiceFromContainer(ModuleSettings::class)->saveEvidenceClasses($evidenceClasses);
        }

        $this->removeEvidenceToEvidenceList($evidenceClass);
    }

    /**
     * Activates evidence by id.
     *
     * @param string $evidenceId Evidence Id. This is should be defined in evidence class.
     */
    public function activateEvidence(string $evidenceId): void
    {
        $countryEvidences = $this->getServiceFromContainer(ModuleSettings::class)->getCountryEvidences();

        if (isset($countryEvidences[$evidenceId]) && $countryEvidences[$evidenceId] !== 1) {
            $countryEvidences[$evidenceId] = 1;
            $this->getServiceFromContainer(ModuleSettings::class)->saveCountryEvidences($countryEvidences);
        }
    }

    /**
     * Deactivates evidence by id.
     *
     * @param string $evidenceId Evidence Id. This is should be defined in evidence class.
     */
    public function deactivateEvidence(string $evidenceId): void
    {
        $countryEvidences = $this->getServiceFromContainer(ModuleSettings::class)->getCountryEvidences();

        if (isset($countryEvidences[$evidenceId]) && $countryEvidences[$evidenceId] !== 0) {
            $countryEvidences[$evidenceId] = 0;
            $this->getServiceFromContainer(ModuleSettings::class)->saveCountryEvidences($countryEvidences);
        }
    }

    /**
     * Adds evidence id to evidence list. If $blActive is set to true, also activates it.
     * If evidence is already in the list, its activation state will not be changed.
     *
     * @param string $evidenceClass Evidence class name.
     * @param bool   $isActive      Whether to activate evidence.
     */
    private function addEvidenceToEvidenceList(string $evidenceClass, bool $isActive): void
    {
        $evidences  = $this->getServiceFromContainer(ModuleSettings::class)->getCountryEvidences();
        $evidenceId = $this->getServiceFromContainer($evidenceClass)->getId();

        if (!isset($evidences[$evidenceId])) {
            $evidences[$evidenceId] = (int) $isActive;
            $this->getServiceFromContainer(ModuleSettings::class)->saveCountryEvidences($evidences);
        }
    }

    /**
     * Adds evidence id to evidence list. If $blActive is set to true, also activates it.
     * If evidence is already in the list, its activation state will not be changed.
     *
     * @param string $evidenceClass Evidence class name.
     */
    private function removeEvidenceToEvidenceList(string $evidenceClass): void
    {
        $evidences  = $this->getServiceFromContainer(ModuleSettings::class)->getCountryEvidences();
        $evidenceId = $this->getServiceFromContainer($evidenceClass)->getId();

        if (isset($evidences[$evidenceId])) {
            unset($evidences[$evidenceId]);
            $this->getServiceFromContainer(ModuleSettings::class)->saveCountryEvidences($evidences);
        }
    }
}
