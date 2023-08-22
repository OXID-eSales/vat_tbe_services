<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Core;

use OxidEsales\Eshop\Core\DbMetaDataHandler;
use OxidEsales\EVatModule\Core\Events;
use PHPUnit\Framework\TestCase;
use oxDb;

/**
 * This is dummy test.
 */
class EventsTest extends TestCase
{
    /**
     * Test for action on activation
     */
    public function testOnActivate()
    {
        oxDb::getDb()->execute('DROP TABLE IF EXISTS `oevattbe_articlevat`');
        oxDb::getDb()->execute('DROP TABLE IF EXISTS `oevattbe_categoryvat`');
        oxDb::getDb()->execute('DROP TABLE IF EXISTS `oevattbe_countryvatgroups`');
        oxDb::getDb()->execute('DROP TABLE IF EXISTS `oevattbe_orderevidences`');
        oxDb::getDb()->execute('TRUNCATE TABLE `oxmigrations_evat`');

        Events::onActivate();

        $oDbMetaDataHandler = oxNew(DbMetaDataHandler::class);

        $this->assertTrue($oDbMetaDataHandler->tableExists('oevattbe_articlevat'));
        $this->assertTrue($oDbMetaDataHandler->tableExists('oevattbe_categoryvat'));
        $this->assertTrue($oDbMetaDataHandler->tableExists('oevattbe_countryvatgroups'));
        $this->assertTrue($oDbMetaDataHandler->tableExists('oevattbe_orderevidences'));

        $aTableFields = array(
            'oxarticles'   => array('oevattbe_istbeservice'),
            'oxcategories'   => array('oevattbe_istbe'),
            'oxcountry' => array('oevattbe_appliestbevat', 'oevattbe_istbevatconfigured'),
            'oxorder' => array('oevattbe_evidenceused'),
            'oxuser' => array('oevattbe_vatinenterdate'),
        );

        foreach ($aTableFields as $sTableName => $aFieldNames) {
            foreach ($aFieldNames as $sFieldName) {
                $this->assertTrue($oDbMetaDataHandler->fieldExists($sFieldName, $sTableName), "Field missing: $sTableName.$sFieldName");
            }
        }

        $blHasVATGroups = (bool) oxDb::getDb()->getOne("SELECT COUNT(*) FROM oevattbe_countryvatgroups");
        $this->assertTrue($blHasVATGroups);

        $blCountriesMarkedAsConfigured = (bool) oxDb::getDb()->getOne("SELECT COUNT(*) FROM oxcountry WHERE oevattbe_appliestbevat = 1 and oevattbe_istbevatconfigured = 1");
        $this->assertTrue($blCountriesMarkedAsConfigured);
    }
}
