<?php
/**
 * This file is part of OXID eSales eVAT module.
 *
 * OXID eSales eVAT module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales eVAT module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales eVAT module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
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
     * Test evidence list caching. Second time evidence list is returned, it should not be recalculated.
     * Evidences must be stored in session, not in local cache.
     *
     * @return oeVATTBETBEUser
     */
    public function testTBEEvidenceListCaching()
    {
        $oConfig = $this->getConfig();
        $oSession = $this->getSession();
        $oSession->setVariable('TBECountryId', null);
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('aOeVATTBECountryEvidences', array('billing_country' => 1));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxcountryid = new oxField('GermanyId');

        /** @var oeVATTBETBEUser $oTBEUser */
        $oTBEUser = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);
        $oTBEUser->getOeVATTBEEvidenceList();

        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array('oeVATTBEGeoLocationEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'geo_location');
        $oUser->oxuser__oxcountryid = new oxField('LithuaniaId');

        $aExpectedList = array(
            'billing_country' => array(
                'name' => 'billing_country',
                'countryId' => 'GermanyId'
            )
        );

        /** @var oeVATTBETBEUser $oTBEUser */
        $oTBEUser = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);
        $this->assertEquals($aExpectedList, $oTBEUser->getOeVATTBEEvidenceList());

        return $oTBEUser;
    }

    /**
     * Test user country id caching. Second time country id is returned, it should not be recalculated.
     * Country id must be stored in session, not in local cache.
     *
     * @param oeVATTBETBEUser $oTBEUser
     *
     * @depends testTBEEvidenceListCaching
     *
     * @return oeVATTBETBEUser
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
     * @param oeVATTBETBEUser $oTBEUser
     *
     * @depends testTBECountryIdCaching
     *
     * @return oeVATTBETBEUser
     */
    public function testTBEEvidenceUsedCaching($oTBEUser)
    {
        $this->assertEquals('billing_country', $oTBEUser->getOeVATTBETbeEvidenceUsed());
        return $oTBEUser;
    }

    /**
     * Country id should be recalculated when it is unset from cache.
     *
     * @param oeVATTBETBEUser $oTBEUser
     *
     * @depends testTBEEvidenceUsedCaching
     */
    public function testUnsetEvidenceListCaching($oTBEUser)
    {
        $oTBEUser->unsetOeVATTBETbeCountryFromCaching();
        $this->assertEquals('', $oTBEUser->getOeVATTBETbeCountryId());
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
     * Testing
     */
    public function testGetCountry()
    {
        $sGermanyId = 'a7c40f631fc920687.20179984';
        $oSession = $this->getSession();
        $oSession->setVariable('TBECountryId', $sGermanyId);

        $oConfig = $this->getConfig();
        $oSession = $this->getSession();

        $oUser = oxNew('oeVATTBETBEUser', oxNew('oxUser'), $oSession, $oConfig);

        $this->assertSame('Deutschland', $oUser->getCountry()->oxcountry__oxtitle->value);
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
