<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model\Evidence\Item;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\Evidence\Item\GeoLocationEvidence;
use OxidEsales\EVatModule\Shop\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for oeVATTBEEvidenceCalculator.
 */
class GeoLocationEvidenceTest extends TestCase
{
    public function testGetId()
    {
        /** @var User|MockObject $oUser */
        $oUser = $this->createMock(User::class);

        $session = Registry::getSession();
        $session->setUser($oUser);

        $oEvidence = new GeoLocationEvidence($session);

        $this->assertEquals('geo_location', $oEvidence->getId());
    }

    public function testGetCountryId()
    {
        /** @var User|MockObject $oUser */
        $oUser = $this->createMock(User::class);

        $session = Registry::getSession();
        $session->setUser($oUser);

        $oEvidence = new GeoLocationEvidence($session);

        $this->assertEquals('', $oEvidence->getCountryId());
    }
}
