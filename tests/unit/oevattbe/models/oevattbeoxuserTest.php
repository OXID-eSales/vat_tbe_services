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

    public function testTBECountryIdSelecting()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('blOeVATTBECountryEvidences', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        $oUser = oxNew('oeVATTBEOxUser');
        $oUser->oxuser__oxcountryid = new oxField('GermanyId');

        $this->assertEquals('GermanyId', $oUser->getTbeCountryId());
    }

    /**
     * Vat id getter test
     */
    public function testGetVatId()
    {
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxustId = new oxField('IdNumber');

        $this->assertSame('IdNumber', $oUser->getVatId());
    }

    /**
     * Vat id getter test
     */
    public function testGetVatIdStoreDate()
    {
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oevattbe_vatidenterdate = new oxField('2014-12-12 12:12:12');

        $this->assertSame('2014-12-12 12:12:12', $oUser->getVatIdStoreDate());
    }

    /**
     * Test for saving vat id store date
     */
    public function testSaveVatIdStoreDateDateIsAlreadySaved()
    {
        $oUser = oxNew('oxUser');
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->oxuser__oxustId = new oxField('IdNumber');
        $oUser->oxuser__oevattbe_vatidenterdate = new oxField('2014-12-12 12:12:12');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');

        $this->assertSame('2014-12-12 12:12:12', $oUser->getVatIdStoreDate());
    }

    /**
     * Test for saving vat id store date
     */
    public function testSaveVatIdDoNotStoreDateVatIdNotSet()
    {
        $oUser = oxNew('oxUser');
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');

        $this->assertSame('0000-00-00 00:00:00', $oUser->getVatIdStoreDate());
    }

    /**
     * Test for saving vat id store date
     */
    public function testSaveVatIdStoreDateDateIsNotSaved()
    {
        $oUtilsDate = $this->getMock("oxUtilsDate", array("getTime"));
        $oUtilsDate->expects($this->any())->method("getTime")->will($this->returnValue(1388664732));

        oxRegistry::set('oxUtilsDate', $oUtilsDate);

        $oUser = oxNew('oxUser');
        $oUser->delete('userId');

        $oUser->setId('userId');
        $oUser->oxuser__oxustId = new oxField('IdNumber');
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load('userId');

        $this->assertSame('2014-01-02 13:12:12', $oUser->getVatIdStoreDate());
    }
}
