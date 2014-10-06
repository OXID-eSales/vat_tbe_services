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
 * @copyright (C) OXID eSales AG 2003-2014T
 */

/**
 * Test class for oeVATTBEEvidenceCalculator.
 *
 * @covers oeVATTBEEvidenceSelector
*/
class Unit_oeVATTBE_Models_oeVATTBEEvidenceSelectorTest extends OxidTestCase
{


    public function providerGetCountryWhenBothEvidenceDoNotMatch()
    {
        $oBillingEvidence = $this->_createEvidence('billing_address', 'Germany');
        $oGeoLocationEvidence = $this->_createEvidence('geo_location', 'Lithuania');
        $oEvidenceList = new oeVATTBEEvidenceList(array($oBillingEvidence, $oGeoLocationEvidence));

        return array(
            array($oEvidenceList, 'billing_address', $oBillingEvidence),
            array($oEvidenceList, 'geo_location', $oGeoLocationEvidence)
        );
    }

    /**
     * @param oeVATTBEEvidenceList $oEvidenceList
     * @param string               $sDefaultEvidence
     * @param oeVATTBEEvidence     $sExpectedEvidence
     *
     * @dataProvider providerGetCountryWhenBothEvidenceDoNotMatch
     */
    public function testGetCountryWhenBothEvidenceDoNotMatchDefaultTaken($oEvidenceList, $sDefaultEvidence, $sExpectedEvidence)
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('sDefaultTBEEvidence', $sDefaultEvidence);

        $oCalculator = new oeVATTBEEvidenceSelector($oEvidenceList, $oConfig);

        $this->assertSame($sExpectedEvidence, $oCalculator->getEvidence());
    }

    public function testGetCountryWhenDefaultEvidenceEmpty()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('sDefaultTBEEvidence', 'default_evidence');

        $oBillingEvidence = $this->_createEvidence('billing_address', 'Germany');
        $oGeoLocationEvidence = $this->_createEvidence('geo_location', 'Lithuania');
        $oDefaultEvidence = $this->_createEvidence('default_evidence', '');
        $oEvidenceList = new oeVATTBEEvidenceList(array($oBillingEvidence, $oGeoLocationEvidence, $oDefaultEvidence));

        $oCalculator = new oeVATTBEEvidenceSelector($oEvidenceList, $oConfig);

        $this->assertSame($oBillingEvidence, $oCalculator->getEvidence());
    }

    public function testGetCountryWhenDefaultAndFirstEvidenceEmpty()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('sDefaultTBEEvidence', 'default_evidence');

        $oBillingEvidence = $this->_createEvidence('billing_address', '');
        $oGeoLocationEvidence = $this->_createEvidence('geo_location', 'Lithuania');
        $oDefaultEvidence = $this->_createEvidence('default_evidence', '');
        $oEvidenceList = new oeVATTBEEvidenceList(array($oBillingEvidence, $oGeoLocationEvidence, $oDefaultEvidence));

        $oCalculator = new oeVATTBEEvidenceSelector($oEvidenceList, $oConfig);

        $this->assertSame($oGeoLocationEvidence, $oCalculator->getEvidence());
    }

    public function testGetCountryWithEmptyList()
    {
        $oConfig = $this->getConfig();
        $oEvidenceList = new oeVATTBEEvidenceList();
        $oCalculator = new oeVATTBEEvidenceSelector($oEvidenceList, $oConfig);

        $this->assertSame(null, $oCalculator->getEvidence());
    }

    public function testIsEvidencesContradictingWhenEvidencesDoNotMatch()
    {
        $oBillingEvidence = $this->_createEvidence('billing_address', 'Germany');
        $oGeoLocationEvidence = $this->_createEvidence('geo_location', 'Germany');
        $oEvidenceList = new oeVATTBEEvidenceList(array($oBillingEvidence, $oGeoLocationEvidence));

        $oCalculator = new oeVATTBEEvidenceSelector($oEvidenceList, $this->getConfig());

        $this->assertSame(false, $oCalculator->isEvidencesContradicting());
    }

    public function testIsEvidencesContradictingWhenEvidencesMatch()
    {
        $oBillingEvidence = $this->_createEvidence('billing_address', 'Germany');
        $oGeoLocationEvidence = $this->_createEvidence('geo_location', 'Lithuania');
        $oEvidenceList = new oeVATTBEEvidenceList(array($oBillingEvidence, $oGeoLocationEvidence));

        $oCalculator = new oeVATTBEEvidenceSelector($oEvidenceList, $this->getConfig());

        $this->assertSame(true, $oCalculator->isEvidencesContradicting());
    }

    /**
     * Creates evidence object with given name and country.
     *
     * @param string $sName
     * @param string $sCountry
     *
     * @return oeVATTBEEvidence
     */
    protected function _createEvidence($sName, $sCountry)
    {
        /** @var oeVATTBEEvidence|PHPUnit_Framework_MockObject_MockObject $oUser */
        $oEvidence = $this->getMock('oeVATTBEEvidence', array('getName', 'getCountryId'), array(), '', false);
        $oEvidence->expects($this->any())->method('getName')->will($this->returnValue($sName));
        $oEvidence->expects($this->any())->method('getCountryId')->will($this->returnValue($sCountry));

        return $oEvidence;
    }
}
