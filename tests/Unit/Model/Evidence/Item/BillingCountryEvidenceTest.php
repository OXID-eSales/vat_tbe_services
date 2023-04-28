<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\VisualCmsModule\Tests\Unit\Model\Evidence\Item;

use PHPUnit\Framework\TestCase;

/**
 * Test class for oeVATTBEEvidenceCalculator.
 *
 * @covers BillingCountryEvidence
 */
class BillingCountryEvidenceTest extends TestCase
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
