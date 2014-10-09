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
 * This is dummy test.
 */
class Unit_oeVATTBE_Core_oeVATTBEEventsTest extends OxidTestCase
{
    /**
     * Tear down
     */
    public function tearDown()
    {
        oxDb::getDb()->execute('DROP TABLE IF EXISTS `oevattbe_articlevat`');
        oxDb::getDb()->execute('DROP TABLE IF EXISTS `oevattbe_countryvatgroups`');

        oeVATTBEEvents::onActivate();

        parent::tearDown();
    }

    /**
     * Test for action on activation
     */
    public function testOnActivate()
    {
        oeVATTBEEvents::onActivate();

        $oDbMetaDataHandler = oxNew('oxDbMetaDataHandler');

        $this->assertTrue($oDbMetaDataHandler->tableExists('oevattbe_articlevat'));
        $this->assertTrue($oDbMetaDataHandler->tableExists('oevattbe_countryvatgroups'));
        $this->assertTrue($oDbMetaDataHandler->tableExists('oevattbe_orderevidences'));

        $aTableFields = array(
            'oxarticles'   => 'oevattbe_istbeservice',
            'oxcountry' => 'oevattbe_appliestbevat',
            'oxcountry' => 'oevattbe_istbevatconfigured',
            'oxorder' => 'oevattbe_hastbeservices',
            'oxuser' => 'oevattbe_vatidenterdate',

        );

        foreach ($aTableFields as $sTableName => $sFieldName) {
            $this->assertTrue($oDbMetaDataHandler->fieldExists($sFieldName, $sTableName));
        }
    }
}
