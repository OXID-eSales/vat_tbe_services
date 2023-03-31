<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Test class for oeVATTBEEvidenceCalculator.
 *
 * @covers oeVATTBEBillingCountryEvidence
 */
class Unit_oeVATTBE_Models_Evidences_Items_oeVATTBEBillingCountryEvidenceTest extends OxidTestCase
{

    public function testGetId()
    {
        $oUser = new oxUser();
        $oEvidence = new oeVATTBEBillingCountryEvidence($oUser);

        $this->assertEquals('billing_country', $oEvidence->getId());
    }

    public function testGetCountryId()
    {
        $oUser = new oxUser();
        $oUser->oxuser__oxcountryid = new oxField('a7c40f631fc920687.20179984');

        $oEvidence = new oeVATTBEBillingCountryEvidence($oUser);

        $this->assertEquals('a7c40f631fc920687.20179984', $oEvidence->getCountryId());
    }

    public function testGetCountryIdWhenNoCountrySet()
    {
        $oUser = new oxUser();

        $oEvidence = new oeVATTBEBillingCountryEvidence($oUser);

        $this->assertEquals(null, $oEvidence->getCountryId());
    }
}
