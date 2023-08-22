<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EVatModule\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230405115029 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $aCountryVATs = [
            'BE' => [
                ['name' => 'Reduce rate 1', 'rate' => 6],
                ['name' => 'Reduce rate 2', 'rate' => 12],
                ['name' => 'Standard rate', 'rate' => 21],
                ['name' => 'Parking rate', 'rate' => 12],
            ],
            'BG' => [
                ['name' => 'Reduce rate', 'rate' => 9],
                ['name' => 'Standard rate', 'rate' => 20],
            ],
            'CZ' => [
                ['name' => 'Reduce rate', 'rate' => 15],
                ['name' => 'Standard rate', 'rate' => 21],
            ],
            'DK' => [
                ['name' => 'Standard rate', 'rate' => 25],
            ],
            'DE' => [
                ['name' => 'Reduce rate', 'rate' => 7],
                ['name' => 'Standard rate', 'rate' => 19],
            ],
            'EE' => [
                ['name' => 'Reduce rate', 'rate' => 9],
                ['name' => 'Standard rate', 'rate' => 20],
            ],
            'GR' => [
                ['name' => 'Reduce rate 1', 'rate' => 6.5],
                ['name' => 'Reduce rate 2', 'rate' => 13],
                ['name' => 'Standard rate', 'rate' => 23],
            ],
            'ES' => [
                ['name' => 'Super reduce rate', 'rate' => 4],
                ['name' => 'Reduce rate', 'rate' => 10],
                ['name' => 'Standard rate', 'rate' => 21],
            ],
            'FR' => [
                ['name' => 'Super reduce rate', 'rate' => 2.1],
                ['name' => 'Reduce rate 1', 'rate' => 5.5],
                ['name' => 'Reduce rate 2', 'rate' => 10],
                ['name' => 'Standard rate', 'rate' => 20],
            ],
            'HR' => [
                ['name' => 'Reduce rate 1', 'rate' => 5],
                ['name' => 'Reduce rate 2', 'rate' => 13],
                ['name' => 'Standard rate', 'rate' => 25],
            ],
            'IE' => [
                ['name' => 'Super reduce rate', 'rate' => 4.8],
                ['name' => 'Reduce rate 1', 'rate' => 9],
                ['name' => 'Reduce rate 2', 'rate' => 13.5],
                ['name' => 'Standard rate', 'rate' => 23],
                ['name' => 'Parking rate', 'rate' => 13.5],
            ],
            'IT' => [
                ['name' => 'Super reduce rate', 'rate' => 4],
                ['name' => 'Reduce rate', 'rate' => 10],
                ['name' => 'Standard rate', 'rate' => 22],
            ],
            'CY' => [
                ['name' => 'Reduce rate 1', 'rate' => 5],
                ['name' => 'Reduce rate 2', 'rate' => 9],
                ['name' => 'Standard rate', 'rate' => 19],
            ],
            'LV' => [
                ['name' => 'Reduce rate', 'rate' => 12],
                ['name' => 'Standard rate', 'rate' => 21],
            ],
            'LT' => [
                ['name' => 'Reduce rate 1', 'rate' => 5],
                ['name' => 'Reduce rate 2', 'rate' => 9],
                ['name' => 'Standard rate', 'rate' => 21],
            ],
            'LU' => [
                ['name' => 'Super reduce rate', 'rate' => 3],
                ['name' => 'Reduce rate', 'rate' => 6],
                ['name' => 'Reduce rate', 'rate' => 12],
                ['name' => 'Standard rate', 'rate' => 15],
                ['name' => 'Parking rate', 'rate' => 12],
            ],
            'HU' => [
                ['name' => 'Reduce rate 1', 'rate' => 5],
                ['name' => 'Reduce rate 2', 'rate' => 18],
                ['name' => 'Standard rate', 'rate' => 27],
            ],
            'MT' => [
                ['name' => 'Reduce rate 1', 'rate' => 5],
                ['name' => 'Reduce rate 2', 'rate' => 7],
                ['name' => 'Standard rate', 'rate' => 18],
            ],
            'NL' => [
                ['name' => 'Reduce rate', 'rate' => 6],
                ['name' => 'Standard rate', 'rate' => 21],
            ],
            'AT' => [
                ['name' => 'Reduce rate', 'rate' => 10],
                ['name' => 'Standard rate', 'rate' => 20],
                ['name' => 'Parking rate', 'rate' => 12],
            ],
            'PL' => [
                ['name' => 'Reduce rate 1', 'rate' => 5],
                ['name' => 'Reduce rate 2', 'rate' => 8],
                ['name' => 'Standard rate', 'rate' => 23],
            ],
            'PT' => [
                ['name' => 'Reduce rate 1', 'rate' => 6],
                ['name' => 'Reduce rate 2', 'rate' => 13],
                ['name' => 'Standard rate', 'rate' => 23],
                ['name' => 'Parking rate', 'rate' => 13],
            ],
            'RO' => [
                ['name' => 'Reduce rate 1', 'rate' => 5],
                ['name' => 'Reduce rate 2', 'rate' => 9],
                ['name' => 'Standard rate', 'rate' => 24],
            ],
            'SI' => [
                ['name' => 'Reduce rate', 'rate' => 9.5],
                ['name' => 'Standard rate', 'rate' => 22],
            ],
            'SK' => [
                ['name' => 'Reduce rate', 'rate' => 10],
                ['name' => 'Standard rate', 'rate' => 20],
            ],
            'FI' => [
                ['name' => 'Reduce rate 1', 'rate' => 10],
                ['name' => 'Reduce rate 2', 'rate' => 14],
                ['name' => 'Standard rate', 'rate' => 24],
            ],
            'SE' => [
                ['name' => 'Reduce rate 1', 'rate' => 6],
                ['name' => 'Reduce rate 2', 'rate' => 12],
                ['name' => 'Standard rate', 'rate' => 25],
            ],
            'GB' => [
                ['name' => 'Reduce rate', 'rate' => 5],
                ['name' => 'Standard rate', 'rate' => 20],
            ],
        ];

        foreach ($aCountryVATs as $sCountryCode => $aVATRates) {

            $isCountryConfigured = $this->connection->fetchOne("SELECT COUNT(*) FROM `oevattbe_countryvatgroups` INNER JOIN `oxcountry` ON oevattbe_countryvatgroups.OEVATTBE_COUNTRYID=oxcountry.oxid AND OXISOALPHA2 = " . $this->connection->quote($sCountryCode));
            if (!$isCountryConfigured) {
                $this->addSql("UPDATE `oxcountry` SET `oevattbe_appliestbevat` = 1, `oevattbe_istbevatconfigured` = 1 WHERE `oxisoalpha2` = " . $this->connection->quote($sCountryCode));

                foreach ($aVATRates as $aRate) {
                    $this->addSql("INSERT INTO `oevattbe_countryvatgroups`
                        SET `oevattbe_countryid` = (SELECT `oxid` FROM `oxcountry` WHERE `oxisoalpha2` = " . $this->connection->quote($sCountryCode) . "),
                        `oevattbe_name` = " . $this->connection->quote($aRate['name']) . ", 
                        `oevattbe_rate` = " . $this->connection->quote($aRate['rate']));
                }
            }
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
