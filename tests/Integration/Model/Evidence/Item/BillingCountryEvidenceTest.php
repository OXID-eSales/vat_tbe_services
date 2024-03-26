<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Model\Evidence\Item;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\Evidence\Item\BillingCountryEvidence;
use OxidEsales\EVatModule\Shop\User;
use PHPUnit\Framework\TestCase;

/**
 * Test class for oeVATTBEEvidenceCalculator.
 */
class BillingCountryEvidenceTest extends TestCase
{
    public function testGetId()
    {
        $oUser = oxNew(User::class);

        $session = Registry::getSession();
        $session->setUser($oUser);

        $oEvidence = new BillingCountryEvidence($session);

        $this->assertEquals('billing_country', $oEvidence->getId());
    }

    public function testGetCountryId()
    {
        $oUser = oxNew(User::class);
        $oUser->assign([
            'oxusername'  => '',
            'oxpassword'  => '',
            'oxregister'  => '',
            'oxcountryid' => 'a7c40f631fc920687.20179984'
        ]);
        $oUser->save();

        $session = Registry::getSession();
        $session->setUser($oUser);

        $oEvidence = new BillingCountryEvidence($session);

        $this->assertEquals('a7c40f631fc920687.20179984', $oEvidence->getCountryId());
    }

    public function testGetCountryIdWhenNoCountrySet()
    {
        $oUser = oxNew(User::class);

        $session = Registry::getSession();
        $session->setUser($oUser);

        $oEvidence = new BillingCountryEvidence($session);

        $this->assertEquals(null, $oEvidence->getCountryId());
    }
}
