<?php

declare(strict_types=1);

namespace OxidEsales\ModuleTemplate\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230404145636 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        if (!$schema->hasTable('oevattbe_articlevat')) {
            $this->addSql("CREATE TABLE IF NOT EXISTS `oevattbe_articlevat` (
              `OEVATTBE_ARTICLEID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_COUNTRYID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_VATGROUPID` int(11) unsigned NOT NULL,
              `OEVATTBE_TIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
              UNIQUE KEY `OEVATTBE_ARTICLEID_2` (`OEVATTBE_ARTICLEID`,`OEVATTBE_COUNTRYID`),
              KEY `OEVATTBE_ARTICLEID` (`OEVATTBE_ARTICLEID`),
              KEY `OEVATTBE_COUNTRYID` (`OEVATTBE_COUNTRYID`),
              KEY `OEVATTBE_VATGROUPID` (`OEVATTBE_VATGROUPID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }

        if (!$schema->hasTable('oevattbe_categoryvat')) {
            $this->addSql("CREATE TABLE IF NOT EXISTS `oevattbe_categoryvat` (
              `OEVATTBE_CATEGORYID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_COUNTRYID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_VATGROUPID` int(11) unsigned NOT NULL,
              `OEVATTBE_TIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
              UNIQUE KEY `OEVATTBE_CATEGORYID_2` (`OEVATTBE_CATEGORYID`,`OEVATTBE_COUNTRYID`),
              KEY `OEVATTBE_CATEGORYID` (`OEVATTBE_CATEGORYID`),
              KEY `OEVATTBE_COUNTRYID` (`OEVATTBE_COUNTRYID`),
              KEY `OEVATTBE_VATGROUPID` (`OEVATTBE_VATGROUPID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }

        if (!$schema->hasTable('oevattbe_countryvatgroups')) {
            $this->addSql("CREATE TABLE IF NOT EXISTS `oevattbe_countryvatgroups` (
              `OEVATTBE_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `OEVATTBE_COUNTRYID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_NAME` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_DESCRIPTION` text NOT NULL,
              `OEVATTBE_RATE` decimal(9,2) NOT NULL,
              `OEVATTBE_TIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
              PRIMARY KEY (`OEVATTBE_ID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }

        if (!$schema->hasTable('oevattbe_orderevidences')) {
            $this->addSql("CREATE TABLE IF NOT EXISTS `oevattbe_orderevidences` (
              `OEVATTBE_ORDERID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_EVIDENCE` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_COUNTRYID` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_TIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
              KEY (`OEVATTBE_ORDERID`),
              KEY (`OEVATTBE_COUNTRYID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }

        $aTableFields = [
            'oxarticles'   => ['oevattbe_istbeservice'],
            'oxcategories' => ['oevattbe_istbe'],
            'oxcountry'    => ['oevattbe_appliestbevat', 'oevattbe_istbevatconfigured'],
            'oxorder'      => ['oevattbe_evidenceused'],
            'oxuser'       => ['oevattbe_vatinenterdate'],
        ];

        $aFieldSql = [
            'oevattbe_istbeservice'       => "ALTER TABLE `oxarticles` ADD `OEVATTBE_ISTBESERVICE` tinyint(1) NOT NULL default 0",
            'oevattbe_istbe'              => "ALTER TABLE `oxcategories` ADD `OEVATTBE_ISTBE` tinyint(1) NOT NULL default 0",
            'oevattbe_appliestbevat'      => "ALTER TABLE `oxcountry` ADD `OEVATTBE_APPLIESTBEVAT` tinyint(1) NOT NULL default 0",
            'oevattbe_istbevatconfigured' => "ALTER TABLE `oxcountry` ADD `OEVATTBE_ISTBEVATCONFIGURED` tinyint(1) NOT NULL default 0",
            'oevattbe_evidenceused'       => "ALTER TABLE `oxorder` ADD `OEVATTBE_EVIDENCEUSED` char(32) NOT NULL default 0",
            'oevattbe_vatinenterdate'     => "ALTER TABLE `oxuser` ADD `OEVATTBE_VATINENTERDATE` timestamp NOT NULL",
        ];

        foreach ($aTableFields as $sTableName => $aFields) {
            $table = $schema->getTable($sTableName);
            foreach ($aFields as $sFieldName) {
                if (!$table->hasColumn($sFieldName)) {
                    $this->addSql($aFieldSql[$sFieldName]);
                }
            }
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
