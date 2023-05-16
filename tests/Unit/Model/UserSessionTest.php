<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EVatModule\Model\Evidence\Item\BillingCountryEvidence;
use OxidEsales\EVatModule\Model\Evidence\Item\GeoLocationEvidence;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Shop\User;
use OxidEsales\Eshop\Application\Model\User as EShopUser;
use OxidEsales\EVatModule\Traits\ServiceContainer;
use PHPUnit\Framework\TestCase;

/**
 * Testing TBEUser class session related functionality.
 *
 * @covers User
 */
class UserSessionTest extends TestCase
{
    use ServiceContainer;

    /**
     * Test evidence list caching. Second time evidence list is returned, it should not be recalculated.
     * Evidences must be stored in session, not in local cache.
     *
     * @return User
     */
    public function testTBEEvidenceListCaching()
    {
        $oConfig = Registry::getConfig();
        $oSession = Registry::getSession();
        $oSession->setVariable('TBECountryId', null);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', ['oeVATTBEBillingCountryEvidence']);
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', ['billing_country' => 1]);
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class]);
        $moduleSettings->saveCountryEvidences(['billing_country' => 1]);
        $moduleSettings->saveDefaultEvidence('billing_country');

        $oUser = oxNew(EShopUser::class);
        $oUser->oxuser__oxcountryid = new Field('GermanyId');

        /** @var User $oTBEUser */
        $oTBEUser = oxNew(User::class, $oUser, $oSession, $oConfig);
        $oTBEUser->getOeVATTBEEvidenceList();

        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', ['oeVATTBEGeoLocationEvidence']);
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'geo_location');

        $moduleSettings->saveEvidenceClasses([GeoLocationEvidence::class]);
        $moduleSettings->saveDefaultEvidence('geo_location');

        $oUser->oxuser__oxcountryid = new Field('LithuaniaId');

        $aExpectedList = [
            'billing_country' => [
                'name'      => 'billing_country',
                'countryId' => 'GermanyId'
            ]
        ];

        /** @var User $oTBEUser */
        $oTBEUser = oxNew(User::class, $oUser, $oSession, $oConfig);
        $this->assertEquals($aExpectedList, $oTBEUser->getOeVATTBEEvidenceList());

        return $oTBEUser;
    }

    /**
     * Test user country id caching. Second time country id is returned, it should not be recalculated.
     * Country id must be stored in session, not in local cache.
     *
     * @param User $oTBEUser
     *
     * @depends testTBEEvidenceListCaching
     *
     * @return User
     */
    public function testTBECountryIdCaching($oTBEUser)
    {
        $this->assertEquals('GermanyId', $oTBEUser->getOeVATTBETbeCountryId());

        return $oTBEUser;
    }

    /**
     * Test used evidence caching. Second time evidence is returned, it should not be recalculated.
     * Evidence must be stored in session, not in local cache.
     *
     * @param User $oTBEUser
     *
     * @depends testTBECountryIdCaching
     *
     * @return User
     */
    public function testTBEEvidenceUsedCaching($oTBEUser)
    {
        $this->assertEquals('billing_country', $oTBEUser->getOeVATTBETbeEvidenceUsed());

        return $oTBEUser;
    }

    /**
     * Country id should be recalculated when it is unset from cache.
     *
     * @param User $oTBEUser
     *
     * @depends testTBEEvidenceUsedCaching
     */
    public function testUnsetEvidenceListCaching($oTBEUser)
    {
        $oTBEUser->unsetOeVATTBETbeCountryFromCaching();
        $this->assertEquals('', $oTBEUser->getOeVATTBETbeCountryId());
    }
}
