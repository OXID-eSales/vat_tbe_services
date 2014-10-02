<?php
/**
 * This file is part of OXID eSales VAT TBE module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales VAT TBE module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */


/**
 * Class defines what module does on Shop events.
 */
class oeVATTBEEvents
{
    /**
     * Execute action on activate event
     */
    public static function onActivate()
    {
        self::_addArticleVatGroupTable();
        self::_addCountryVatGroupTable();
        self::_addFields();
    }

    /**
     * Execute action on deactivate event
     */
    public static function onDeactivate()
    {
    }

    /**
     * Add fields to oxArticle table
     */
    protected static function _addFields()
    {
        $oDbMetaDataHandler = oxNew('oxDbMetaDataHandler');

        $aTableFields = array(
            'oxarticles'  => 'oevattbe_istbeservice',
            'oxcountry' => 'oevattbe_appliestbevat',
            'oxcountry' => 'oevattbe_istbevatconfigured',
        );

        $aFieldSql = array(
            'oevattbe_istbeservice' => "ALTER TABLE `oxarticles` ADD `oevattbe_istbeservice` tinyint(1) NOT NULL default 0",
            'oevattbe_appliestbevat' => "ALTER TABLE `oxcountry` ADD `oevattbe_appliestbevat` tinyint(1) NOT NULL default 0",
            'oevattbe_istbevatconfigured' => "ALTER TABLE `oxcountry` ADD `oevattbe_istbevatconfigured` tinyint(1) NOT NULL default 0",
        );

        foreach ($aTableFields as $sTableName => $sFieldName) {
            if (!$oDbMetaDataHandler->fieldExists($sFieldName, $sTableName)) {
                oxDb::getDb()->execute($aFieldSql[$sFieldName]);
            }
        }
    }

    /**
     * Add article VAT group table
     */
    protected static function _addArticleVatGroupTable()
    {
        $sSql = "CREATE TABLE IF NOT EXISTS `oevattbe_articlevat` (
              `OEVATTBE_ARTICLEID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_COUNTRYID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `OEVATTBE_VATGROUPID` int(11) unsigned NOT NULL,
              `OEVATTBE_TIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
              KEY `OEVATTBE_ARTICLEID` (`OEVATTBE_ARTICLEID`),
              KEY `OEVATTBE_COUNTRYID` (`OEVATTBE_COUNTRYID`),
              KEY `OEVATTBE_VATGROUPID` (`OEVATTBE_VATGROUPID`)
            ) ENGINE=InnoDB;";

        oxDb::getDb()->execute($sSql);
    }

    /**
     * Add country VAT group table
     */
    protected static function _addCountryVatGroupTable()
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
}
