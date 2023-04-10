<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\VisualCmsModule\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxUser class.
 */
class Unit_oeVatTbe_models_oeVATTBEOxUserTest extends TestCase
{
    /**
     * Select Country test
     */
    public function testTBECountryIdSelecting()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', array('billing_country' => 1));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxcountryid = new oxField('GermanyId');

        $this->assertEquals('GermanyId', $oUser->getOeVATTBETbeCountryId());
    }

    /**
     * Vat id getter test
     */
    public function testGetOeVATTBEVatIn()
    {
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxustid = new oxField('IdNumber');

        $this->assertSame('IdNumber', $oUser->getOeVATTBEVatIn());
    }

    /**
     * Vat id getter test
     */
    public function testGetOeVATTBEVatInStoreDate()
    {
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oevattbe_vatinenterdate = new oxField('2014-12-12 12:12:12');

        $this->assertSame('2014-12-12 12:12:12', $oUser->getOeVATTBEVatInStoreDate());
    }

    /**
     * On User creation:
     * a) if VAT IN in set - date is stored date;
     */
    public function testSaveVatInStoreDateOnNewUserCreation()
    {
        $oUtilsDate = $this->getMock("oxUtilsDate", array("getTime"));
        $oUtilsDate->expects($this->any())->method("getTime")->will($this->returnValue(1388664732));

        oxRegistry::set('oxUtilsDate', $oUtilsDate);

        $oUser = oxNew('oxUser');
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->oxuser__oxustid = new oxField('IdNumber');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');

        $this->assertSame('2014-01-02 13:12:12', $oUser->getOeVATTBEVatInStoreDate());
    }

    /**
     * On User creation:
     * b) if VAT IN not set - date is not stored;
     */
    public function testSaveVatInDoNotStoreDateVatIdNotSet()
    {
        $oUser = oxNew('oxUser');
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');

        $this->assertSame('0000-00-00 00:00:00', $oUser->getOeVATTBEVatInStoreDate());
    }

    /**
     * On User info change:
     * a) old data VAT IN is not set, after VAT IN is set - date stored
     */
    public function testSaveVatInStoreDateA()
    {
        $oUtilsDate = $this->getMock("oxUtilsDate", array("getTime"));
        $oUtilsDate->expects($this->any())->method("getTime")->will($this->returnValue(1388664732));

        oxRegistry::set('oxUtilsDate', $oUtilsDate);

        $oUser = oxNew('oxUser');
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');
        $oUser->oxuser__oxustid = new oxField('IdNumber');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');

        $this->assertSame('2014-01-02 13:12:12', $oUser->getOeVATTBEVatInStoreDate());
    }

    /**
     * On User info change:
     * b) old data VAT IN is not set, after VAT IN is not set - date not stored
     */
    public function testSaveVatInStoreDateB()
    {
        $oUtilsDate = $this->getMock("oxUtilsDate", array("getTime"));
        $oUtilsDate->expects($this->any())->method("getTime")->will($this->returnValue(1388664732));

        oxRegistry::set('oxUtilsDate', $oUtilsDate);

        $oUser = oxNew('oxUser');
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');

        $this->assertSame('0000-00-00 00:00:00', $oUser->getOeVATTBEVatInStoreDate());
    }

    /**
     * On User info change:
     * c) old data VAT IN is set, date not set -  after update - date is stored
     */
    public function testSaveVatInStoreDateC()
    {
        $oUtilsDate = $this->getMock("oxUtilsDate", array("getTime"));
        $oUtilsDate->expects($this->any())->method("getTime")->will($this->returnValue(1388664732));

        oxRegistry::set('oxUtilsDate', $oUtilsDate);

        $oUser = oxNew('oxUser');
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->oxuser__oxustid = new oxField('IdNumber');
        $oUser->oxuser__oevattbe_vatinenterdate = new oxField('0000-00-00 00:00:00');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');
        $oUser->oxuser__oxustid = new oxField('IdNumber2');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');

        $this->assertSame('2014-01-02 13:12:12', $oUser->getOeVATTBEVatInStoreDate());
    }

    /**
     * On User info change:
     * d) old data VAT IN is set, date not set - after VAT IN removed - date is not stored
     */
    public function testSaveVatInStoreD()
    {
        $oUser = oxNew('oxUser');
        $oUser->delete('userId');

        $oUser->setId('userId');
        $oUser->oxuser__oxustid = new oxField('IdNumber');
        $oUser->save();

        //removing set date
        oxDb::getDb()->execute("UPDATE `oxuser` SET `oevattbe_vatinenterdate` = '0000-00-00 00:00:00' WHERE `oxid` = 'userId'");

        $oUser = oxNew('oxUser');
        $oUser->load('userId');
        $oUser->oxuser__oxustid = new oxField('');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');

        $this->assertSame('0000-00-00 00:00:00', $oUser->getOeVATTBEVatInStoreDate());
    }

    /**
     * On User info change:
     * e) old data VAT IN is set, date set  - after VAT IN is removed - date not changed
     */
    public function testSaveVatInStoreDateE()
    {
        $oUser = oxNew('oxUser');
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->oxuser__oxustid = new oxField('IdNumber');
        $oUser->oxuser__oevattbe_vatinenterdate = new oxField('2014-12-12 12:12:12');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');
        $oUser->oxuser__oxustid = new oxField('');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');
        $this->assertSame('2014-12-12 12:12:12', $oUser->getOeVATTBEVatInStoreDate());
    }

    /**
     * On User info change:
     * f) old data VAT IN is set, date set  - after VAT IN updated - date not changed
     */
    public function testSaveVatInStoreDateF()
    {
        $oUser = oxNew('oxUser');
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->oxuser__oxustid = new oxField('IdNumber');
        $oUser->oxuser__oevattbe_vatinenterdate = new oxField('2014-12-12 12:12:12');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');
        $oUser->oxuser__oxustid = new oxField('IdNumber2');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');

        $this->assertSame('2014-12-12 12:12:12', $oUser->getOeVATTBEVatInStoreDate());
    }
}
