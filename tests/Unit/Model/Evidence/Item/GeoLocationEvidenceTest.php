<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model\Evidence\Item;

use PHPUnit\Framework\TestCase;

/**
 * Test class for oeVATTBEEvidenceCalculator.
 *
 * @covers GeoLocationEvidence
 */
class GeoLocationEvidenceTest extends TestCase
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
