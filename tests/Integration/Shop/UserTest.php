<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Shop;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsDate;
use OxidEsales\EVatModule\Model\Evidence\Item\BillingCountryEvidence;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;
use OxidEsales\EVatModule\Traits\ServiceContainer;
use oxDb;

/**
 * Testing extended oxUser class.
 */
class UserTest extends BaseTestCase
{
    use ServiceContainer;

    public function setUp(): void
    {
        parent::setUp();

        \oxDb::getDb()->execute("TRUNCATE TABLE `oxuser`;");
    }

    /**
     * Select Country test
     */
    public function testTBECountryIdSelecting()
    {
        Registry::getSession()->setVariable('TBECountryId', null);

        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class]);
        $moduleSettings->saveCountryEvidences(['billing_country' => 1]);
        $moduleSettings->saveDefaultEvidence('billing_country');

        $oUser = oxNew(User::class);
        $oUser->assign([
           'oxcountryid' => 'GermanyId'
        ]);
        Registry::getSession()->setUser($oUser);

        $this->assertEquals('GermanyId', $oUser->getOeVATTBETbeCountryId());
    }

    /**
     * Vat id getter test
     */
    public function testGetOeVATTBEVatIn()
    {
        $oUser = oxNew(User::class);
        $oUser->assign([
            'oxustid' => 'IdNumber'
        ]);

        $this->assertSame('IdNumber', $oUser->getOeVATTBEVatIn());
    }

    /**
     * Vat id getter test
     */
    public function testGetOeVATTBEVatInStoreDate()
    {
        $oUser = oxNew(User::class);
        $oUser->assign([
            'oevattbe_vatinenterdate' => '2014-12-12 12:12:12'
        ]);

        $this->assertSame('2014-12-12 12:12:12', $oUser->getOeVATTBEVatInStoreDate());
    }

    /**
     * On User creation:
     * a) if VAT IN in set - date is stored date;
     */
    public function testSaveVatInStoreDateOnNewUserCreation()
    {
        $oUtilsDate = $this->createPartialMock(UtilsDate::class, ["getTime"]);
        $oUtilsDate->expects($this->any())->method("getTime")->will($this->returnValue(1388664732));

        Registry::set(UtilsDate::class, $oUtilsDate);

        $oUser = oxNew(User::class);
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->assign([
            'oxustid' => 'IdNumber',
        ]);
        $oUser->save();

        $oUser = oxNew(User::class);
        $oUser->load('userId');

        $this->assertSame('2014-01-02 13:12:12', $oUser->getOeVATTBEVatInStoreDate());
    }

    /**
     * On User creation:
     * b) if VAT IN not set - date is not stored;
     */
    public function testSaveVatInDoNotStoreDateVatIdNotSet()
    {
        $oUser = oxNew(User::class);
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->save();

        $oUser = oxNew(User::class);
        $oUser->load('userId');

        $this->assertSame('0000-00-00 00:00:00', $oUser->getOeVATTBEVatInStoreDate());
    }

    /**
     * On User info change:
     * a) old data VAT IN is not set, after VAT IN is set - date stored
     */
    public function testSaveVatInStoreDateA()
    {
        $oUtilsDate = $this->createPartialMock(UtilsDate::class, ["getTime"]);
        $oUtilsDate->expects($this->any())->method("getTime")->will($this->returnValue(1388664732));

        Registry::set(UtilsDate::class, $oUtilsDate);

        $oUser = oxNew(User::class);
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->save();

        $oUser = oxNew(User::class);
        $oUser->load('userId');
        $oUser->assign([
            'oxustid' => 'IdNumber'
        ]);
        $oUser->save();

        $oUser = oxNew(User::class);
        $oUser->load('userId');

        $this->assertSame('2014-01-02 13:12:12', $oUser->getOeVATTBEVatInStoreDate());
    }

    /**
     * On User info change:
     * b) old data VAT IN is not set, after VAT IN is not set - date not stored
     */
    public function testSaveVatInStoreDateB()
    {
        $oUtilsDate = $this->createPartialMock(UtilsDate::class, ["getTime"]);
        $oUtilsDate->expects($this->any())->method("getTime")->will($this->returnValue(1388664732));

        Registry::set(UtilsDate::class, $oUtilsDate);

        $oUser = oxNew(User::class);
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->save();

        $oUser = oxNew(User::class);
        $oUser->load('userId');
        $oUser->save();

        $oUser = oxNew(User::class);
        $oUser->load('userId');

        $this->assertSame('0000-00-00 00:00:00', $oUser->getOeVATTBEVatInStoreDate());
    }

    /**
     * On User info change:
     * c) old data VAT IN is set, date not set -  after update - date is stored
     */
    public function testSaveVatInStoreDateC()
    {
        $oUtilsDate = $this->createPartialMock(UtilsDate::class, ["getTime"]);
        $oUtilsDate->expects($this->any())->method("getTime")->will($this->returnValue(1388664732));

        Registry::set(UtilsDate::class, $oUtilsDate);

        $oUser = oxNew(User::class);
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->assign([
            'oxustid' => 'IdNumber',
            'oevattbe_vatinenterdate' => '0000-00-00 00:00:00',
        ]);
        $oUser->save();

        $oUser = oxNew(User::class);
        $oUser->load('userId');
        $oUser->assign([
            'oxustid' => 'IdNumber2'
        ]);
        $oUser->save();

        $oUser = oxNew(User::class);
        $oUser->load('userId');

        $this->assertSame('2014-01-02 13:12:12', $oUser->getOeVATTBEVatInStoreDate());
    }

    /**
     * On User info change:
     * d) old data VAT IN is set, date not set - after VAT IN removed - date is not stored
     */
    public function testSaveVatInStoreD()
    {
        $oUser = oxNew(User::class);
        $oUser->delete('userId');

        $oUser->setId('userId');
        $oUser->assign([
            'oxustid' => 'IdNumber'
        ]);
        $oUser->save();

        //removing set date
        oxDb::getDb()->execute("UPDATE `oxuser` SET `oevattbe_vatinenterdate` = '0000-00-00 00:00:00' WHERE `oxid` = 'userId'");

        $oUser = oxNew(User::class);
        $oUser->load('userId');
        $oUser->assign([
            'oxustid' => ''
        ]);
        $oUser->save();

        $oUser = oxNew(User::class);
        $oUser->load('userId');

        $this->assertSame('0000-00-00 00:00:00', $oUser->getOeVATTBEVatInStoreDate());
    }

    /**
     * On User info change:
     * e) old data VAT IN is set, date set  - after VAT IN is removed - date not changed
     */
    public function testSaveVatInStoreDateE()
    {
        $oUser = oxNew(User::class);
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->assign([
            'oxustid' => 'IdNumber',
            'oevattbe_vatinenterdate' => '2014-12-12 12:12:12',
        ]);
        $oUser->save();

        $oUser = oxNew(User::class);
        $oUser->load('userId');
        $oUser->assign([
            'oxustid' => ''
        ]);
        $oUser->save();

        $oUser = oxNew(User::class);
        $oUser->load('userId');
        $this->assertSame('2014-12-12 12:12:12', $oUser->getOeVATTBEVatInStoreDate());
    }

    /**
     * On User info change:
     * f) old data VAT IN is set, date set  - after VAT IN updated - date not changed
     */
    public function testSaveVatInStoreDateF()
    {
        $oUser = oxNew(User::class);
        $oUser->delete('userId');
        $oUser->setId('userId');
        $oUser->assign([
            'oxustid'                 => 'IdNumber',
            'oevattbe_vatinenterdate' => '2014-12-12 12:12:12',
        ]);
        $oUser->save();

        $oUser = oxNew(User::class);
        $oUser->load('userId');
        $oUser->assign([
            'oxustid' => 'IdNumber2'
        ]);
        $oUser->save();

        $oUser = oxNew(User::class);
        $oUser->load('userId');

        $this->assertSame('2014-12-12 12:12:12', $oUser->getOeVATTBEVatInStoreDate());
    }
}
