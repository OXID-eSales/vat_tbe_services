<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EVatModule\Model\Evidence\Item\BillingCountryEvidence;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Model\User as UserModel;
use OxidEsales\EVatModule\Shop\User;
use OxidEsales\Eshop\Application\Model\User as EShopUser;
use OxidEsales\EVatModule\Traits\ServiceContainer;
use PHPUnit\Framework\TestCase;

/**
 * Testing TBEUser class.
 *
 * @covers \OxidEsales\EVatModule\Model\User
 */
class UserTest extends TestCase
{
    use ServiceContainer;

    /**
     * Tests collecting of TBE evidences when evidence collector is billing country and it is set as default.
     *
     * @return UserModel
     */
    public function testCollectingTBEEvidenceList()
    {
        //TODO: tmp solution, fix after moving to integration test
        ContainerFactory::resetContainer();

        $oConfig = Registry::getConfig();
        $oSession = Registry::getSession();
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', ['oeVATTBEBillingCountryEvidence']);
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', ['billing_country' => 1]);
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class]);
        $moduleSettings->saveCountryEvidences(['billing_country' => 1]);
        $moduleSettings->saveDefaultEvidence('billing_country');

        $oUser = oxNew(EShopUser::class);
        $oUser->oxuser__oxcountryid = new Field('GermanyId');
        Registry::getSession()->setUser($oUser);

        /** @var UserModel $oTBEUser */
        $oTBEUser = oxNew(UserModel::class, $oSession, $oConfig);

        $aExpected = [
            'billing_country' => [
                'name'      => 'billing_country',
                'countryId' => 'GermanyId'
            ],
        ];
        $this->assertEquals($aExpected, $oTBEUser->getOeVATTBEEvidenceList());

        return $oTBEUser;
    }

    /**
     * Test selection of country id from evidence list.
     *
     * @param User $oTBEUser
     *
     * @depends testCollectingTBEEvidenceList
     *
     * @return User
     */
    public function testTBECountryIdSelecting($oTBEUser)
    {
        $this->assertEquals('GermanyId', $oTBEUser->getOeVATTBETbeCountryId());

        return $oTBEUser;
    }

    /**
     * Test if correct evidence is used for selecting user country from evidence list.
     *
     * @param User $oTBEUser
     *
     * @depends testTBECountryIdSelecting
     */
    public function testTBEEvidenceUsedSelecting($oTBEUser)
    {
        $this->assertEquals('billing_country', $oTBEUser->getOeVATTBETbeEvidenceUsed());
    }

    /**
     * Tests collecting of evidences when no evidence collectors are registered.
     *
     * @return User
     */
    public function testCollectingOfTBEEvidenceListWhenEvidenceListIsEmpty()
    {
        $oConfig = Registry::getConfig();
        $oSession = Registry::getSession();
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', []);
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', '');

        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveEvidenceClasses([]);
        $moduleSettings->saveDefaultEvidence('');

        $oUser = oxNew(EShopUser::class);

        /** @var User $oTBEUser */
        $oTBEUser = oxNew(User::class, $oUser, $oSession, $oConfig);

        $this->assertEquals([], $oTBEUser->getOeVATTBEEvidenceList());

        return $oTBEUser;
    }

    /**
     * Tests selecting of evidence when no evidences are found.
     *
     * @param User $oTBEUser
     *
     * @depends testCollectingOfTBEEvidenceListWhenEvidenceListIsEmpty
     *
     * @return User
     */
    public function testTBECountryIdSelectingWhenNoEvidenceFound($oTBEUser)
    {
        $this->assertEquals('', $oTBEUser->getOeVATTBETbeCountryId());

        return $oTBEUser;
    }

    /**
     * Tests returning of evidence collector used for selecting user country when no evidences are found.
     *
     * @param User $oTBEUser
     *
     * @depends testTBECountryIdSelectingWhenNoEvidenceFound
     */
    public function testTBEEvidenceUsedSelectingWhenNoEvidenceFound($oTBEUser)
    {
        $this->assertEquals('', $oTBEUser->getOeVATTBETbeEvidenceUsed());
    }

    /**
     * Country should be cached even when it is not found. It should not be rechecked on every request.
     */
    public function testCountryIdCachingWhenCountryIsNotFound()
    {
        $oConfig = Registry::getConfig();
        $oSession = Registry::getSession();
        $oSession->setVariable('TBECountryId', null);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', ['oeVATTBEBillingCountryEvidence']);
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class]);
        $moduleSettings->saveDefaultEvidence('billing_country');

        $oUser = oxNew(EShopUser::class);
        $oUser->oxuser__oxcountryid = new Field('');
        $oSession->setUser($oUser);

        $oTBEUser = oxNew(User::class, $oUser, $oSession, $oConfig);
        $oTBEUser->getOeVATTBETbeCountryId();
        $oUser->oxuser__oxcountryid = new Field('LithuaniaId');

        $this->assertEquals('', $oTBEUser->getOeVATTBETbeCountryId());
    }

    /**
     * Provider for testGetCountry.
     *
     * @return array
     */
    public function providerGetCountry(): array
    {
        return [
            [''],
            ['NonExistingCountryId'],
        ];
    }

    /**
     * Testing getting of country when invalid user country id is set.
     *
     * @param string $sCountryId
     *
     * @dataProvider providerGetCountry
     */
    public function testGetCountryWithInvalidCountryId($sCountryId)
    {
        $oSession = Registry::getSession();
        $oSession->setVariable('TBECountryId', $sCountryId);

        $oConfig = Registry::getConfig();
        $oSession = Registry::getSession();

//        $oUser = oxNew(UserModel::class, oxNew(EShopUser::class), $oSession, $oConfig);
        $oUser = oxNew(UserModel::class, $oSession, $oConfig);

        $this->assertNull($oUser->getCountry());
    }

    /**
     * Testing getting of country when valid user country id is set.
     */
    public function testGetCountryWithValidCountryId()
    {
        $sGermanyId = 'a7c40f631fc920687.20179984';
        $oSession = Registry::getSession();
        $oSession->setVariable('TBECountryId', $sGermanyId);

        $oConfig = Registry::getConfig();
        $oSession = Registry::getSession();

//        $oUser = oxNew(UserModel::class, oxNew(EShopUser::class), $oSession, $oConfig);
        $oUser = oxNew(UserModel::class, $oSession, $oConfig);

        $this->assertSame('Deutschland', $oUser->getCountry()->getFieldData('oxtitle'));
    }

    /**
     * Testing isUserFromDomesticCountry when user is from domestic country.
     */
    public function testIsUserFromDomesticCountryWhenCountriesMatch()
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('sOeVATTBEDomesticCountry', 'LT');

        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveDomesticCountry('LT');

        $sLithuaniaId = '8f241f11095d6ffa8.86593236';
        $oSession = Registry::getSession();
        $oSession->setVariable('TBECountryId', $sLithuaniaId);

        //TODO: Set user in session
//        $oUser = oxNew(UserModel::class, oxNew(EShopUser::class), $oSession, $oConfig);
        $oUser = oxNew(UserModel::class, $oSession, $oConfig);

        $this->assertSame(true, $oUser->isUserFromDomesticCountry());
    }

    /**
     * Not matching countries provider for testIsUserFromDomesticCountryWhenCountriesDoesNotMatch
     *
     * @return array
     */
    public function providerIsUserFromDomesticCountryWhenCountriesDoesNotMatch(): array
    {
        return [
            ['LT', 'a7c40f631fc920687.20179984'],
            ['', 'a7c40f631fc920687.20179984'],
            ['LT', ''],
            ['LT', 'LT'],
            ['', ''],
        ];
    }

    /**
     * Testing isUserFromDomesticCountry when user is from domestic country.
     *
     * @param string $sDomesticCountryAbbr Domestic country abbreviation.
     * @param string $sUserCountryId       User country id.
     *
     * @dataProvider providerIsUserFromDomesticCountryWhenCountriesDoesNotMatch
     */
    public function testIsUserFromDomesticCountryWhenCountriesDoesNotMatch($sDomesticCountryAbbr, $sUserCountryId)
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('sOeVATTBEDomesticCountry', $sDomesticCountryAbbr);

        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveDomesticCountry($sDomesticCountryAbbr);

        $oSession = Registry::getSession();
        $oSession->setVariable('TBECountryId', $sUserCountryId);

//        $oUser = oxNew(UserModel::class, oxNew(EShopUser::class), $oSession, $oConfig);
        $oUser = oxNew(UserModel::class, $oSession, $oConfig);

        $this->assertSame(false, $oUser->isUserFromDomesticCountry());
    }
}
