<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model\Evidence\Item;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EVatModule\Model\Evidence\Item\GeoLocationEvidence;
use OxidEsales\EVatModule\Shop\User;
use PHPUnit\Framework\MockObject\MockObject;
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
        $session = Registry::getSession();

        /** @var User|MockObject $oUser */
        $oUser = $this->createMock(User::class);
        $session->setUser($oUser);

        //TODO: check if set user in session is necessary

        $oEvidence = new GeoLocationEvidence($session);

        $this->assertEquals('geo_location', $oEvidence->getId());
    }

    public function testGetCountryId()
    {
        $session = Registry::getSession();

        /** @var User|MockObject $oUser */
        $oUser = $this->createMock(User::class);
        $session->setUser($oUser);

        $oEvidence = new GeoLocationEvidence($session);

        $this->assertEquals('', $oEvidence->getCountryId());
    }
}
