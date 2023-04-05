<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Core;

use OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder;
use OxidEsales\Eshop\Core\DbMetaDataHandler;
use OxidEsales\EVatModule\Model\Evidence\Item\BillingCountryEvidence;
use OxidEsales\EVatModule\Model\Evidence\EvidenceRegister;
use OxidEsales\EVatModule\Model\Evidence\Item\GeoLocationEvidence;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class defines what module does on Shop events.
 */
class Events
{
    /**
     * Execute action on activate event
     */
    public static function onActivate(): void
    {
        // execute module migrations
        self::executeModuleMigrations();
        self::regenerateDatabaseViews();

        /** @var EvidenceRegister $evidenceRegister */
        $evidenceRegister = oxNew(EvidenceRegister::class);
        $evidenceRegister->registerEvidence(BillingCountryEvidence::class, true);
        $evidenceRegister->registerEvidence(GeoLocationEvidence::class, true);
    }

    /**
     * Execute action on deactivate event
     */
    public static function onDeactivate(): void
    {
    }

    /**
     * Execute necessary module migrations on activate event
     */
    private static function executeModuleMigrations(): void
    {
        $migrations = (new MigrationsBuilder())->build();

        $output = new BufferedOutput();
        $migrations->setOutput($output);
        $neeedsUpdate = $migrations->execute('migrations:up-to-date', 'oevattbe');

        if ($neeedsUpdate) {
            $migrations->execute('migrations:migrate', 'oevattbe');
        }
    }

    public static function regenerateDatabaseViews(): void
    {
        $metaDataHandler = oxNew(DbMetaDataHandler::class);
        $metaDataHandler->updateViews();
    }
}
