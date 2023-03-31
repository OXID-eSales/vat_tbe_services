<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Test class for oeVATTBEEvidenceCalculator.
 *
 * @covers oeVATTBEGeoLocationEvidence
 */
class Unit_oeVATTBE_Models_Evidences_Items_oeVATTBEGeoLocationEvidenceTest extends OxidTestCase
{

    public function testGetId()
    {
        /** @var oxUser|PHPUnit_Framework_MockObject_MockObject $oUser */
        $oUser = $this->getMock('oeVATTBEOxUser', array(), array(), '', false);
        $oEvidence = new oeVATTBEGeoLocationEvidence($oUser);

        $this->assertEquals('geo_location', $oEvidence->getId());
    }

    public function testGetCountryId()
    {
        /** @var oxUser|PHPUnit_Framework_MockObject_MockObject $oUser */
        $oUser = $this->getMock('oeVATTBEOxUser', array(), array(), '', false);
        $oEvidence = new oeVATTBEGeoLocationEvidence($oUser);

        $this->assertEquals('', $oEvidence->getCountryId());
    }
}
