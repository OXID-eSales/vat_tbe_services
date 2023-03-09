<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EVatModule\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface;
use OxidEsales\EVatModule\Core\Module;

/**
 * @extendable-class
 */
class ModuleSettings
{
    public const COUNTRY_EVIDENCES = 'aOeVATTBECountryEvidences';

    public const DEFAULT_EVIDENCE = 'sOeVATTBEDefaultEvidence';

    public const DOMESTIC_COUNTRY = 'sOeVATTBEDomesticCountry';

    public function __construct(
        private ModuleSettingServiceInterface $moduleSettingService
    ) {
    }

    public function getCountryEvidences(): array
    {
        return $this->moduleSettingService->getCollection(self::COUNTRY_EVIDENCES, Module::MODULE_ID);
    }

    public function saveCountryEvidences(array $value): void
    {
        $this->moduleSettingService->saveCollection(self::COUNTRY_EVIDENCES, $value, Module::MODULE_ID);
    }

    public function getDefaultEvidence(): string
    {
        return $this->moduleSettingService
            ->getString(self::DEFAULT_EVIDENCE, Module::MODULE_ID)
            ->trim()
            ->toString();
    }

    public function saveDefaultEvidence(string $value): void
    {
        $this->moduleSettingService->saveString(self::DEFAULT_EVIDENCE, $value, Module::MODULE_ID);
    }

    public function getDomesticCountry(): string
    {
        return $this->moduleSettingService
            ->getString(self::DOMESTIC_COUNTRY, Module::MODULE_ID)
            ->trim()
            ->toString();
    }

    public function saveDomesticCountry(string $value): void
    {
        $this->moduleSettingService->saveString(self::DOMESTIC_COUNTRY, $value, Module::MODULE_ID);
    }
}