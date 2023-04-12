<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model\Evidence\Item;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\Evidence\Item\BillingCountryEvidence;
use OxidEsales\EVatModule\Shop\User;
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
        $oUser = oxNew(User::class);
//        $oEvidence = new BillingCountryEvidence($oUser);

        //TODO: Set user in session if necessary
        $oEvidence = new BillingCountryEvidence(Registry::getSession());

        $this->assertEquals('billing_country', $oEvidence->getId());
    }

    public function testGetCountryId()
    {
        $oUser = oxNew(User::class);
        $oUser->oxuser__oxcountryid = new Field('a7c40f631fc920687.20179984');

//        $oEvidence = new BillingCountryEvidence($oUser);

        $oEvidence = new BillingCountryEvidence(Registry::getSession());

        $this->assertEquals('a7c40f631fc920687.20179984', $oEvidence->getCountryId());
    }

    public function testGetCountryIdWhenNoCountrySet()
    {
        $oUser = oxNew(User::class);
//        $oEvidence = new BillingCountryEvidence($oUser);

        $oEvidence = new BillingCountryEvidence(Registry::getSession());

        $this->assertEquals(null, $oEvidence->getCountryId());
    }
}
