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
 * Testing extended oxArticle class.
 */
class Unit_oeVatTbe_models_oeVATTBETBEUserTest extends OxidTestCase
{
    public function testTBECountryIdSelecting()
    {
        $oConfig = $this->getConfig();
        $oSession = $this->getSession();
        $oConfig->setConfigParam('TBECountryEvidences', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sDefaultTBEEvidence', 'billing_country');

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxcountryid = new oxField('GermanyId');

        $oTBEUser = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);

        $this->assertEquals('GermanyId', $oTBEUser->getTbeCountryId());
    }

    public function testInformationAddedToSession()
    {
        $oConfig = $this->getConfig();
        $oSession = $this->getSession();
        $oConfig->setConfigParam('TBECountryEvidences', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sDefaultTBEEvidence', 'billing_country');

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
        $oConfig->setConfigParam('TBECountryEvidences', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sDefaultTBEEvidence', 'billing_country');


        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxcountryid = new oxField('GermanyId');

        $oTBEUser = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);
        $oTBEUser->getTbeCountryId();
        $oUser->oxuser__oxcountryid = new oxField('LithuaniaId');

        $this->assertEquals('GermanyId', $oTBEUser->getTbeCountryId());
    }
}
