<?php
/**
 * This file is part of OXID eSales eVAT module.
 *
 * OXID eSales eVAT module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales eVAT module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales eVAT module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */

namespace OxidEsales\EVatModule\Core;

use \oxDb;
use OxidEsales\Eshop\Core\DbMetaDataHandler;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\Evidence\Item\BillingCountryEvidence;
use OxidEsales\EVatModule\Model\Evidence\EvidenceRegister;
use OxidEsales\EVatModule\Model\Evidence\Item\GeoLocationEvidence;

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
        self::addArticleVatGroupTable();
        self::addCategoryVatGroupTable();
        self::addCountryVatGroupTable();
        self::addOrderEvidences();
        self::addFields();
        self::regenerateViews();
        self::configureCountries();

        /** @var EvidenceRegister $oEvidenceRegister */
        $oEvidenceRegister = oxNew(EvidenceRegister::class, Registry::getConfig());
        $oEvidenceRegister->registerEvidence(BillingCountryEvidence::class, true);
        $oEvidenceRegister->registerEvidence(GeoLocationEvidence::class, true);
    }

    /**
     * Execute action on deactivate event
     */
    public static function onDeactivate(): void
    {
    }

    /**
     * Add fields to oxArticle table
     */
    protected static function addFields(): void
    {
        $oDbMetaDataHandler = oxNew(DbMetaDataHandler::class);

        $aTableFields = array(
            'oxarticles'  => array('oevattbe_istbeservice'),
            'oxcategories'  => array('oevattbe_istbe'),
            'oxcountry' => array('oevattbe_appliestbevat', 'oevattbe_istbevatconfigured'),
            'oxorder' => array('oevattbe_evidenceused'),
            'oxuser' => array('oevattbe_vatinenterdate'),
        );

        $aFieldSql = array(
            'oevattbe_istbeservice' => "ALTER TABLE `oxarticles` ADD `OEVATTBE_ISTBESERVICE` tinyint(1) NOT NULL default 0",
            'oevattbe_istbe' => "ALTER TABLE `oxcategories` ADD `OEVATTBE_ISTBE` tinyint(1) NOT NULL default 0",
            'oevattbe_appliestbevat' => "ALTER TABLE `oxcountry` ADD `OEVATTBE_APPLIESTBEVAT` tinyint(1) NOT NULL default 0",
            'oevattbe_istbevatconfigured' => "ALTER TABLE `oxcountry` ADD `OEVATTBE_ISTBEVATCONFIGURED` tinyint(1) NOT NULL default 0",
            'oevattbe_evidenceused' => "ALTER TABLE `oxorder` ADD `OEVATTBE_EVIDENCEUSED` char(32) NOT NULL default 0",
            'oevattbe_vatinenterdate' => "ALTER TABLE `oxuser` ADD `OEVATTBE_VATINENTERDATE` timestamp NOT NULL",
        );

        foreach ($aTableFields as $sTableName => $aFields) {
            foreach ($aFields as $sFieldName) {
                if (!$oDbMetaDataHandler->fieldExists($sFieldName, $sTableName)) {
                    oxDb::getDb()->execute($aFieldSql[$sFieldName]);
                }
            }
        }
    }

    /**
     * Add article VAT group table
     */
    protected static function addArticleVatGroupTable(): void
    {
        $sSql = "CREATE TABLE IF NOT EXISTS `oevattbe_articlevat` (
              `OEVATTBE_ARTICLEID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_COUNTRYID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_VATGROUPID` int(11) unsigned NOT NULL,
              `OEVATTBE_TIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
              UNIQUE KEY `OEVATTBE_ARTICLEID_2` (`OEVATTBE_ARTICLEID`,`OEVATTBE_COUNTRYID`),
              KEY `OEVATTBE_ARTICLEID` (`OEVATTBE_ARTICLEID`),
              KEY `OEVATTBE_COUNTRYID` (`OEVATTBE_COUNTRYID`),
              KEY `OEVATTBE_VATGROUPID` (`OEVATTBE_VATGROUPID`)
            ) ENGINE=InnoDB;";

        oxDb::getDb()->execute($sSql);
    }

    /**
     * Add category VAT group table
     */
    protected static function addCategoryVatGroupTable(): void
    {
        $sSql = "CREATE TABLE IF NOT EXISTS `oevattbe_categoryvat` (
              `OEVATTBE_CATEGORYID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_COUNTRYID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_VATGROUPID` int(11) unsigned NOT NULL,
              `OEVATTBE_TIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
              UNIQUE KEY `OEVATTBE_CATEGORYID_2` (`OEVATTBE_CATEGORYID`,`OEVATTBE_COUNTRYID`),
              KEY `OEVATTBE_CATEGORYID` (`OEVATTBE_CATEGORYID`),
              KEY `OEVATTBE_COUNTRYID` (`OEVATTBE_COUNTRYID`),
              KEY `OEVATTBE_VATGROUPID` (`OEVATTBE_VATGROUPID`)
            ) ENGINE=InnoDB;";

        oxDb::getDb()->execute($sSql);
    }

    /**
     * Add country VAT group table
     */
    protected static function addCountryVatGroupTable(): void
    {
        $sSql = "CREATE TABLE IF NOT EXISTS `oevattbe_countryvatgroups` (
              `OEVATTBE_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `OEVATTBE_COUNTRYID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_NAME` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_DESCRIPTION` text NOT NULL,
              `OEVATTBE_RATE` decimal(9,2) NOT NULL,
              `OEVATTBE_TIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
              PRIMARY KEY (`OEVATTBE_ID`)
            ) ENGINE=InnoDB;";

        oxDb::getDb()->execute($sSql);
    }

    /**
     * Add order evidence table
     */
    protected static function addOrderEvidences(): void
    {
        $sSql = "CREATE TABLE IF NOT EXISTS `oevattbe_orderevidences` (
              `OEVATTBE_ORDERID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_EVIDENCE` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_COUNTRYID` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_TIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
              KEY (`OEVATTBE_ORDERID`),
              KEY (`OEVATTBE_COUNTRYID`)
            ) ENGINE=InnoDB;";

        oxDb::getDb()->execute($sSql);
    }

    /**
     * regenerate views for changed tables
     */
    protected static function regenerateViews(): void
    {
        $oDbMetaDataHandler = oxNew(DbMetaDataHandler::class);
        $oDbMetaDataHandler->updateViews();
    }

    /**
     * insert demo data
     */
    protected static function addDemoData(): void
    {
        $oDb = oxDb::getDb();

        $aSqls = array();

        $aSqls[] = "INSERT INTO oevattbe_articlevat SET OEVATTBE_ARTICLEID = 'b56369b1fc9d7b97f9c5fc343b349ece', OEVATTBE_COUNTRYID = 'a7c40f631fc920687.20179984', OEVATTBE_VATGROUPID = '10'";
        $aSqls[] = "INSERT INTO oevattbe_articlevat SET OEVATTBE_ARTICLEID = 'b56597806428de2f58b1c6c7d3e0e093', OEVATTBE_COUNTRYID = 'a7c40f631fc920687.20179984', OEVATTBE_VATGROUPID = '11'";
        $aSqls[] = "UPDATE oxarticles SET oevattbe_istbeservice = '1' WHERE oxid in ( 'b56369b1fc9d7b97f9c5fc343b349ece', 'b56597806428de2f58b1c6c7d3e0e093' )";

        foreach ($aSqls as $sSql) {
            $oDb->execute($sSql);
        }
    }

    /**
     * insert demo data
     */
    protected static function configureCountries(): void
    {
        $aCountryVATs = self::getCountryVatRates();
        $oDb = oxDb::getDb();

        foreach ($aCountryVATs as $sCountryCode => $aVATRates) {

            $isCountryConfigured = $oDb->getOne("SELECT COUNT(*) FROM `oevattbe_countryvatgroups` INNER JOIN `oxcountry` ON oevattbe_countryvatgroups.OEVATTBE_COUNTRYID=oxcountry.oxid AND OXISOALPHA2 = ". $oDb->quote($sCountryCode));
            if (!$isCountryConfigured) {
                $oDb->execute("UPDATE `oxcountry` SET `oevattbe_appliestbevat` = 1, `oevattbe_istbevatconfigured` = 1 WHERE `oxisoalpha2`=". $oDb->quote($sCountryCode));

                foreach ($aVATRates as $aRate) {
                    $sSql = "INSERT INTO `oevattbe_countryvatgroups` ";
                    $sSql .= "SET ";
                    $sSql .= "`oevattbe_countryid` = (SELECT `oxid` FROM `oxcountry` WHERE `oxisoalpha2`=". $oDb->quote($sCountryCode).")";
                    $sSql .= ",`oevattbe_name` = " . $oDb->quote($aRate['name']);
                    $sSql .= ",`oevattbe_rate` = " . $oDb->quote($aRate['rate']);

                    $oDb->execute($sSql);
                }
            }
        }
    }

    /**
     * Returns EU country vat rates
     *
     * @return array
     */
    protected static function getCountryVatRates(): array
    {
        $aCountryVATs = array(
            'BE' => array(
                array('name' => 'Reduce rate 1', 'rate' => 6),
                array('name' => 'Reduce rate 2', 'rate' => 12),
                array('name' => 'Standard rate', 'rate' => 21),
                array('name' => 'Parking rate', 'rate' => 12),
            ),
            'BG' => array(
                array('name' => 'Reduce rate', 'rate' => 9),
                array('name' => 'Standard rate', 'rate' => 20),
            ),
            'CZ' => array(
                array('name' => 'Reduce rate', 'rate' => 15),
                array('name' => 'Standard rate', 'rate' => 21),
            ),
            'DK' => array(
                array('name' => 'Standard rate', 'rate' => 25),
            ),
            'DE' => array(
                array('name' => 'Reduce rate', 'rate' => 7),
                array('name' => 'Standard rate', 'rate' => 19),
            ),
            'EE' => array(
                array('name' => 'Reduce rate', 'rate' => 9),
                array('name' => 'Standard rate', 'rate' => 20),
            ),
            'GR' => array(
                array('name' => 'Reduce rate 1', 'rate' => 6.5),
                array('name' => 'Reduce rate 2', 'rate' => 13),
                array('name' => 'Standard rate', 'rate' => 23),
            ),
            'ES' => array(
                array('name' => 'Super reduce rate', 'rate' => 4),
                array('name' => 'Reduce rate', 'rate' => 10),
                array('name' => 'Standard rate', 'rate' => 21),
            ),
            'FR' => array(
                array('name' => 'Super reduce rate', 'rate' => 2.1),
                array('name' => 'Reduce rate 1', 'rate' => 5.5),
                array('name' => 'Reduce rate 2', 'rate' => 10),
                array('name' => 'Standard rate', 'rate' => 20),
            ),
            'HR' => array(
                array('name' => 'Reduce rate 1', 'rate' => 5),
                array('name' => 'Reduce rate 2', 'rate' => 13),
                array('name' => 'Standard rate', 'rate' => 25),
            ),
            'IE' => array(
                array('name' => 'Super reduce rate', 'rate' => 4.8),
                array('name' => 'Reduce rate 1', 'rate' => 9),
                array('name' => 'Reduce rate 2', 'rate' => 13.5),
                array('name' => 'Standard rate', 'rate' => 23),
                array('name' => 'Parking rate', 'rate' => 13.5),
            ),
            'IT' => array(
                array('name' => 'Super reduce rate', 'rate' => 4),
                array('name' => 'Reduce rate', 'rate' => 10),
                array('name' => 'Standard rate', 'rate' => 22),
            ),
            'CY' => array(
                array('name' => 'Reduce rate 1', 'rate' => 5),
                array('name' => 'Reduce rate 2', 'rate' => 9),
                array('name' => 'Standard rate', 'rate' => 19),
            ),
            'LV' => array(
                array('name' => 'Reduce rate', 'rate' => 12),
                array('name' => 'Standard rate', 'rate' => 21),
            ),
            'LT' => array(
                array('name' => 'Reduce rate 1', 'rate' => 5),
                array('name' => 'Reduce rate 2', 'rate' => 9),
                array('name' => 'Standard rate', 'rate' => 21),
            ),
            'LU' => array(
                array('name' => 'Super reduce rate', 'rate' => 3),
                array('name' => 'Reduce rate', 'rate' => 6),
                array('name' => 'Reduce rate', 'rate' => 12),
                array('name' => 'Standard rate', 'rate' => 15),
                array('name' => 'Parking rate', 'rate' => 12),
            ),
            'HU' => array(
                array('name' => 'Reduce rate 1', 'rate' => 5),
                array('name' => 'Reduce rate 2', 'rate' => 18),
                array('name' => 'Standard rate', 'rate' => 27),
            ),
            'MT' => array(
                array('name' => 'Reduce rate 1', 'rate' => 5),
                array('name' => 'Reduce rate 2', 'rate' => 7),
                array('name' => 'Standard rate', 'rate' => 18),
            ),
            'NL' => array(
                array('name' => 'Reduce rate', 'rate' => 6),
                array('name' => 'Standard rate', 'rate' => 21),
            ),
            'AT' => array(
                array('name' => 'Reduce rate', 'rate' => 10),
                array('name' => 'Standard rate', 'rate' => 20),
                array('name' => 'Parking rate', 'rate' => 12),
            ),
            'PL' => array(
                array('name' => 'Reduce rate 1', 'rate' => 5),
                array('name' => 'Reduce rate 2', 'rate' => 8),
                array('name' => 'Standard rate', 'rate' => 23),
            ),
            'PT' => array(
                array('name' => 'Reduce rate 1', 'rate' => 6),
                array('name' => 'Reduce rate 2', 'rate' => 13),
                array('name' => 'Standard rate', 'rate' => 23),
                array('name' => 'Parking rate', 'rate' => 13),
            ),
            'RO' => array(
                array('name' => 'Reduce rate 1', 'rate' => 5),
                array('name' => 'Reduce rate 2', 'rate' => 9),
                array('name' => 'Standard rate', 'rate' => 24),
            ),
            'SI' => array(
                array('name' => 'Reduce rate', 'rate' => 9.5),
                array('name' => 'Standard rate', 'rate' => 22),
            ),
            'SK' => array(
                array('name' => 'Reduce rate', 'rate' => 10),
                array('name' => 'Standard rate', 'rate' => 20),
            ),
            'FI' => array(
                array('name' => 'Reduce rate 1', 'rate' => 10),
                array('name' => 'Reduce rate 2', 'rate' => 14),
                array('name' => 'Standard rate', 'rate' => 24),
            ),
            'SE' => array(
                array('name' => 'Reduce rate 1', 'rate' => 6),
                array('name' => 'Reduce rate 2', 'rate' => 12),
                array('name' => 'Standard rate', 'rate' => 25),
            ),
            'GB' => array(
                array('name' => 'Reduce rate', 'rate' => 5),
                array('name' => 'Standard rate', 'rate' => 20),
            ),
        );

        return $aCountryVATs;
    }
}
