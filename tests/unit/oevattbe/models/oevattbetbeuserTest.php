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

    public function testCollectingTBEEvidenceList()
    {
        $oConfig = $this->getConfig();
        $oSession = $this->getSession();
        $oConfig->setConfigParam('blOeVATTBECountryEvidences', array('oeVATTBEBillingCountryEvidence'));
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
        $this->assertEquals($aExpected, $oTBEUser->getTBEEvidenceList());

        return $oTBEUser;
    }

    /**
     * @param oeVATTBETBEUser $oTBEUser
     *
     * @depends testCollectingTBEEvidenceList
     */
    public function testTBECountryIdSelecting($oTBEUser)
    {
        $this->assertEquals('GermanyId', $oTBEUser->getTbeCountryId());
    }

    /**
     * @param oeVATTBETBEUser $oTBEUser
     *
     * @depends testCollectingTBEEvidenceList
     */
    public function testTBEEvidenceUsedSelecting($oTBEUser)
    {
        $this->assertEquals('billing_country', $oTBEUser->getTbeEvidenceUsed());
    }

    public function testCollectingOfTBEEvidenceListWhenEvidenceListIsEmpty()
    {
        $oConfig = $this->getConfig();
        $oSession = $this->getSession();
        $oConfig->setConfigParam('blOeVATTBECountryEvidences', array());
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', '');

        $oUser = oxNew('oxUser');
        /** @var oeVATTBETBEUser $oTBEUser */
        $oTBEUser = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);

        $this->assertEquals(array(), $oTBEUser->getTBEEvidenceList());

        return $oTBEUser;
    }

    /**
     * @param oeVATTBETBEUser $oTBEUser
     *
     * @depends testCollectingOfTBEEvidenceListWhenEvidenceListIsEmpty
     */
    public function testTBECountryIdSelectingWhenNoEvidenceFound($oTBEUser)
    {
        $this->assertEquals('', $oTBEUser->getTbeCountryId());
    }

    /**
     * @param oeVATTBETBEUser $oTBEUser
     *
     * @depends testCollectingOfTBEEvidenceListWhenEvidenceListIsEmpty
     */
    public function testTBEEvidenceUsedSelectingWhenNoEvidenceFound($oTBEUser)
    {
        $this->assertEquals('', $oTBEUser->getTbeEvidenceUsed());
    }

    public function testTBEEvidenceListCaching()
    {
        $oConfig = $this->getConfig();
        $oSession = $this->getSession();
        $oSession->setVariable('TBECountryId', null);
        $oConfig->setConfigParam('blOeVATTBECountryEvidences', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxcountryid = new oxField('GermanyId');

        $oTBEUser = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);
        $oTBEUser->getTBEEvidenceList();

        $oConfig->setConfigParam('blOeVATTBECountryEvidences', array('oeVATTBEGeoLocationEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'geo_location');
        $oUser->oxuser__oxcountryid = new oxField('LithuaniaId');

        $aExpectedList = array(
            'billing_country' => array(
                'name' => 'billing_country',
                'countryId' => 'GermanyId'
            )
        );
        $this->assertEquals($aExpectedList, $oTBEUser->getTBEEvidenceList());

        $this->_hasDependencies = true;

        return $oTBEUser;
    }

    /**
     * @param oeVATTBETBEUser $oTBEUser
     *
     * @depends testTBEEvidenceListCaching
     *
     * @return oeVATTBETBEUser
     */
    public function testTBECountryIdCaching($oTBEUser)
    {
        $this->assertEquals('GermanyId', $oTBEUser->getTbeCountryId());
        return $oTBEUser;
    }

    /**
     * @param oeVATTBETBEUser $oTBEUser
     *
     * @depends testTBEEvidenceListCaching
     */
    public function testTBEEvidenceUsedCaching($oTBEUser)
    {
        $this->assertEquals('billing_country', $oTBEUser->getTbeEvidenceUsed());
    }

    /**
     * @param oeVATTBETBEUser $oTBEUser
     *
     * @depends testTBECountryIdCaching
     */
    public function testUnsetEvidenceListCaching($oTBEUser)
    {
        $oTBEUser->unsetTbeCountryFromCaching();
        $this->assertEquals('LithuaniaId', $oTBEUser->getTbeCountryId());
    }

    public function testCountryIdCachingWhenCountryIsNotFound()
    {
        $oConfig = $this->getConfig();
        $oSession = $this->getSession();
        $oSession->setVariable('TBECountryId', null);
        $oConfig->setConfigParam('blOeVATTBECountryEvidences', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxcountryid = new oxField('');

        $oTBEUser = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);
        $oTBEUser->getTbeCountryId();
        $oUser->oxuser__oxcountryid = new oxField('LithuaniaId');

        $this->assertEquals('', $oTBEUser->getTbeCountryId());

        $this->_hasDependencies = true;

        return $oTBEUser;
    }
}
