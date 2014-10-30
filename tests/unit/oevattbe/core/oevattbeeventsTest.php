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

/**
 * This is dummy test.
 *
 * @covers oeVATTBEEvents
 */
class Unit_oeVATTBE_Core_oeVATTBEEventsTest extends OxidTestCase
{
    /**
     * Tear down
     */
    public function tearDown()
    {
        oxDb::getDb()->execute('DROP TABLE IF EXISTS `oevattbe_articlevat`');
        oxDb::getDb()->execute('DROP TABLE IF EXISTS `oevattbe_categoryvat`');
        oxDb::getDb()->execute('DROP TABLE IF EXISTS `oevattbe_countryvatgroups`');
        oxDb::getDb()->execute('DROP TABLE IF EXISTS `oevattbe_orderevidences`');

        oeVATTBEEvents::onActivate();

        parent::tearDown();
    }

    /**
     * Test for action on activation
     */
    public function testOnActivate()
    {
        oxDb::getDb()->execute('DROP TABLE IF EXISTS `oevattbe_articlevat`');
        oxDb::getDb()->execute('DROP TABLE IF EXISTS `oevattbe_categoryvat`');
        oxDb::getDb()->execute('DROP TABLE IF EXISTS `oevattbe_countryvatgroups`');
        oxDb::getDb()->execute('DROP TABLE IF EXISTS `oevattbe_orderevidences`');

        oeVATTBEEvents::onActivate();

        $oDbMetaDataHandler = oxNew('oxDbMetaDataHandler');

        $this->assertTrue($oDbMetaDataHandler->tableExists('oevattbe_articlevat'));
        $this->assertTrue($oDbMetaDataHandler->tableExists('oevattbe_categoryvat'));
        $this->assertTrue($oDbMetaDataHandler->tableExists('oevattbe_countryvatgroups'));
        $this->assertTrue($oDbMetaDataHandler->tableExists('oevattbe_orderevidences'));

        $aTableFields = array(
            'oxarticles'   => array('oevattbe_istbeservice'),
            'oxcategories'   => array('oevattbe_istbe'),
            'oxcountry' => array('oevattbe_appliestbevat', 'oevattbe_istbevatconfigured'),
            'oxorder' => array('oevattbe_hastbeservices', 'oevattbe_evidenceused'),
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
