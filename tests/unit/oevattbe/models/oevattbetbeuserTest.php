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
 * Testing TBEUser class.
 */
class Unit_oeVatTbe_models_oeVATTBETBEUserTest extends OxidTestCase
{
    private $_hasDependencies = false;

    public function tearDown()
    {
        if (!$this->_hasDependencies) {
            parent::tearDown();
        }
        $this->_hasDependencies = false;
    }

    public function testTBECountryIdSelecting()
    {
        $oConfig = $this->getConfig();
        $oSession = $this->getSession();
        $oConfig->setConfigParam('blOeVATTBECountryEvidences', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxcountryid = new oxField('GermanyId');

        $oTBEUser = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);

        $this->assertEquals('GermanyId', $oTBEUser->getTbeCountryId());
    }

    public function testTBECountryIdWhenNoEvidenceIsSet()
    {
        $oConfig = $this->getConfig();
        $oSession = $this->getSession();
        $oConfig->setConfigParam('blOeVATTBECountryEvidences', array());
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', '');

        $oUser = oxNew('oxUser');
        $oTBEUser = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);

        $this->assertEquals('', $oTBEUser->getTbeCountryId());
    }

    public function testInformationAddedToSession()
    {
        $oConfig = $this->getConfig();
        $oSession = $this->getSession();
        $oConfig->setConfigParam('blOeVATTBECountryEvidences', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxcountryid = new oxField('GermanyId');

        $oTBEUser = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);
        $oTBEUser->getTbeCountryId();

        $this->assertEquals('GermanyId', $oSession->getVariable('TBECountryId'));
        $this->assertEquals('billing_country', $oSession->getVariable('TBEEvidenceUsed'));

        $aExpectedEvidences = array(
            'billing_country' => array(
                'name' => 'billing_country',
                'countryId' => 'GermanyId',
            )
        );

        $this->assertEquals($aExpectedEvidences, $oSession->getVariable('TBEEvidenceList'));
    }

    public function testCountryCaching()
    {
        $oConfig = $this->getConfig();
        $oSession = $this->getSession();
        $oConfig->setConfigParam('blOeVATTBECountryEvidences', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');


        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxcountryid = new oxField('GermanyId');

        $oTBEUser = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);
        $oTBEUser->getTbeCountryId();
        $oUser->oxuser__oxcountryid = new oxField('LithuaniaId');

        $this->assertEquals('GermanyId', $oTBEUser->getTbeCountryId());

        $this->_hasDependencies = true;

        return $oTBEUser;
    }

    /**
     * @param oeVATTBETBEUser $oTBEUser
     *
     * @depends testCountryCaching
     */
    public function testUnsetCountryCaching($oTBEUser)
    {
        $oTBEUser->unsetTbeCountryFromCaching();
        $this->assertEquals('LithuaniaId', $oTBEUser->getTbeCountryId());
    }

    public function testIdentificationIfUserIsLocalWhenUserIsLocal()
    {
        $oConfig = $this->getConfig();
        $oSession = $this->getSession();
        $oConfig->setConfigParam('blOeVATTBECountryEvidences', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');
        $oConfig->setConfigParam('aHomeCountry', array('LithuaniaId', 'GermanyId'));

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxcountryid = new oxField('GermanyId');

        $oTBEUser = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);

        $this->assertEquals(true, $oTBEUser->isLocalUser());
    }

    public function testIdentificationIfUserIsLocalWhenUserIsNotLocal()
    {
        $oConfig = $this->getConfig();
        $oSession = $this->getSession();
        $oConfig->setConfigParam('blOeVATTBECountryEvidences', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');
        $oConfig->setConfigParam('aHomeCountry', array('LithuaniaId', 'FranceId'));

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxcountryid = new oxField('GermanyId');

        $oTBEUser = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);

        $this->assertEquals(false, $oTBEUser->isLocalUser());
    }
}
