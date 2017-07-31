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
 * Testing TBEUser class session related functionality.
 *
 * @covers oeVATTBETBEUser
 */
class Unit_oeVatTbe_models_oeVATTBETBEUserSessionTest extends OxidTestCase
{
    protected $backupGlobalsBlacklist = array('_SESSION');

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
}
