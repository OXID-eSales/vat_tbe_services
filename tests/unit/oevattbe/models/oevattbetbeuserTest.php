<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Testing TBEUser class.
 *
 * @covers oeVATTBETBEUser
 */
class Unit_oeVatTbe_models_oeVATTBETBEUserTest extends OxidTestCase
{

    /**
     * Tests collecting of TBE evidences when evidence collector is billing country and it is set as default.
     *
     * @return oeVATTBETBEUser
     */
    public function testCollectingTBEEvidenceList()
    {
        $oConfig = $this->getConfig();
        $oSession = $this->getSession();
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', array('billing_country' => 1));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxcountryid = new oxField('GermanyId');

        /** @var oeVATTBETBEUser $oTBEUser */
        $oTBEUser = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);

        $aExpected = array(
            'billing_country' => array(
                'name' => 'billing_country', 'countryId' => 'GermanyId'
            ),
        );
        $this->assertEquals($aExpected, $oTBEUser->getOeVATTBEEvidenceList());

        return $oTBEUser;
    }

    /**
     * Test selection of country id from evidence list.
     *
     * @param oeVATTBETBEUser $oTBEUser
     *
     * @depends testCollectingTBEEvidenceList
     *
     * @return oeVATTBETBEUser
     */
    public function testTBECountryIdSelecting($oTBEUser)
    {
        $this->assertEquals('GermanyId', $oTBEUser->getOeVATTBETbeCountryId());
        return $oTBEUser;
    }

    /**
     * Test if correct evidence is used for selecting user country from evidence list.
     *
     * @param oeVATTBETBEUser $oTBEUser
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
     * @return oeVATTBETBEUser
     */
    public function testCollectingOfTBEEvidenceListWhenEvidenceListIsEmpty()
    {
        $oConfig = $this->getConfig();
        $oSession = $this->getSession();
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array());
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', '');

        $oUser = oxNew('oxUser');
        /** @var oeVATTBETBEUser $oTBEUser */
        $oTBEUser = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);

        $this->assertEquals(array(), $oTBEUser->getOeVATTBEEvidenceList());
        return $oTBEUser;
    }

    /**
     * Tests selecting of evidence when no evidences are found.
     *
     * @param oeVATTBETBEUser $oTBEUser
     *
     * @depends testCollectingOfTBEEvidenceListWhenEvidenceListIsEmpty
     *
     * @return oeVATTBETBEUser
     */
    public function testTBECountryIdSelectingWhenNoEvidenceFound($oTBEUser)
    {
        $this->assertEquals('', $oTBEUser->getOeVATTBETbeCountryId());
        return $oTBEUser;
    }

    /**
     * Tests returning of evidence collector used for selecting user country when no evidences are found.
     *
     * @param oeVATTBETBEUser $oTBEUser
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
        $oConfig = $this->getConfig();
        $oSession = $this->getSession();
        $oSession->setVariable('TBECountryId', null);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxcountryid = new oxField('');

        $oTBEUser = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);
        $oTBEUser->getOeVATTBETbeCountryId();
        $oUser->oxuser__oxcountryid = new oxField('LithuaniaId');

        $this->assertEquals('', $oTBEUser->getOeVATTBETbeCountryId());
    }

    /**
     * Provider for testGetCountry.
     *
     * @return array
     */
    public function providerGetCountry()
    {
        return array(
            array(''),
            array('NonExistingCountryId'),
        );
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
        $oSession = $this->getSession();
        $oSession->setVariable('TBECountryId', $sCountryId);

        $oConfig = $this->getConfig();
        $oSession = $this->getSession();

        $oUser = oxNew('oeVATTBETBEUser', oxNew('oxUser'), $oSession, $oConfig);

        $this->assertNull($oUser->getCountry());
    }

    /**
     * Testing getting of country when valid user country id is set.
     */
    public function testGetCountryWithValidCountryId()
    {
        $sGermanyId = 'a7c40f631fc920687.20179984';
        $oSession = $this->getSession();
        $oSession->setVariable('TBECountryId', $sGermanyId);

        $oConfig = $this->getConfig();
        $oSession = $this->getSession();

        $oUser = oxNew('oeVATTBETBEUser', oxNew('oxUser'), $oSession, $oConfig);

        $this->assertSame('Deutschland', $oUser->getCountry()->getFieldData('oxtitle'));
    }

    /**
     * Testing isUserFromDomesticCountry when user is from domestic country.
     */
    public function testIsUserFromDomesticCountryWhenCountriesMatch()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('sOeVATTBEDomesticCountry', 'LT');

        $sLithuaniaId = '8f241f11095d6ffa8.86593236';
        $oSession = $this->getSession();
        $oSession->setVariable('TBECountryId', $sLithuaniaId);

        $oUser = oxNew('oeVATTBETBEUser', oxNew('oxUser'), $oSession, $oConfig);

        $this->assertSame(true, $oUser->isUserFromDomesticCountry());
    }

    /**
     * Not matching countries provider for testIsUserFromDomesticCountryWhenCountriesDoesNotMatch
     *
     * @return array
     */
    public function providerIsUserFromDomesticCountryWhenCountriesDoesNotMatch()
    {
        return array(
            array('LT', 'a7c40f631fc920687.20179984'),
            array('', 'a7c40f631fc920687.20179984'),
            array('LT', ''),
            array('LT', 'LT'),
            array('', ''),
        );
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
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('sOeVATTBEDomesticCountry', $sDomesticCountryAbbr);

        $oSession = $this->getSession();
        $oSession->setVariable('TBECountryId', $sUserCountryId);

        $oUser = oxNew('oeVATTBETBEUser', oxNew('oxUser'), $oSession, $oConfig);

        $this->assertSame(false, $oUser->isUserFromDomesticCountry());
    }

    /**
     * Testing creation of instance with creation method.
     */
    public function testCreateInstance()
    {
        $oUserCountry = oeVATTBETBEUser::createInstance();

        $this->assertInstanceOf('oeVATTBETBEUser', $oUserCountry);
    }
}
