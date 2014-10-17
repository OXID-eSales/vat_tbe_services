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
 * Testing extended oxUser class.
 */
class Unit_oeVatTbe_models_oeVATTBEOxUserTest extends OxidTestCase
{

    /**
     * Select Country test
     */
    public function testTBECountryIdSelecting()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        $oUser = oxNew('oeVATTBEOxUser');
        $oUser->oxuser__oxcountryid = new oxField('GermanyId');

        $this->assertEquals('GermanyId', $oUser->getTbeCountryId());
    }

    /**
     * Vat id getter test
     */
    public function testGetVatIn()
    {
        $oUser = oxNew('oeVATTBEOxUser');
        $oUser->oxuser__oxustid = new oxField('IdNumber');

        $this->assertSame('IdNumber', $oUser->getVatIn());
    }

    /**
     * Vat id getter test
     */
    public function testGetVatInStoreDate()
    {
        $oUser = oxNew('oeVATTBEOxUser');
        $oUser->oxuser__oevattbe_vatinenterdate = new oxField('2014-12-12 12:12:12');

        $this->assertSame('2014-12-12 12:12:12', $oUser->getVatInStoreDate());
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

        $oUser = oxNew('oeVATTBEOxUser');
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->oxuser__oxustid = new oxField('IdNumber');
        $oUser->save();

        $oUser = oxNew('oeVATTBEOxUser');
        $oUser->load('userId');

        $this->assertSame('2014-01-02 13:12:12', $oUser->getVatInStoreDate());
    }

    /**
     * On User creation:
     * b) if VAT IN not set - date is not stored;
     */
    public function testSaveVatInDoNotStoreDateVatIdNotSet()
    {
        $oUser = oxNew('oeVATTBEOxUser');
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->save();

        $oUser = oxNew('oeVATTBEOxUser');
        $oUser->load('userId');

        $this->assertSame('0000-00-00 00:00:00', $oUser->getVatInStoreDate());
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

        $oUser = oxNew('oeVATTBEOxUser');
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');
        $oUser->oxuser__oxustid = new oxField('IdNumber');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');

        $this->assertSame('2014-01-02 13:12:12', $oUser->getVatInStoreDate());
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

        $this->assertSame('0000-00-00 00:00:00', $oUser->getVatInStoreDate());
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

        $oUser = oxNew('oeVATTBEOxUser');
        $oUser->load('userId');
        $oUser->oxuser__oxustid = new oxField('IdNumber2');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');

        $this->assertSame('2014-01-02 13:12:12', $oUser->getVatInStoreDate());
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

        $this->assertSame('0000-00-00 00:00:00', $oUser->getVatInStoreDate());
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
        $this->assertSame('2014-12-12 12:12:12', $oUser->getVatInStoreDate());
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

        $this->assertSame('2014-12-12 12:12:12', $oUser->getVatInStoreDate());
    }
}
