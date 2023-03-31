<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

require_once  __DIR__ . '/../../../../../models/evidences/items/oevattbeevidence.php';
require_once  __DIR__ . '/../../../../../models/evidences/oevattbeevidencelist.php';

/**
 * Test class for oeVATTBEEvidenceCalculator.
 *
 * @covers oeVATTBEEvidenceSelector
*/
class Unit_oeVATTBE_Models_Evidences_oeVATTBEEvidenceSelectorTest extends \OxidEsales\TestingLibrary\UnitTestCase
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
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', $sDefaultEvidence);

        $oCalculator = new oeVATTBEEvidenceSelector($oEvidenceList, $oConfig);

        $this->assertSame($sExpectedEvidence, $oCalculator->getEvidence());
    }

    public function testGetCountryWhenDefaultEvidenceEmpty()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'default_evidence');

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
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'default_evidence');

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
        $oEvidence = $this->createMock(oeVATTBEEvidence::class);
        $oEvidence->expects($this->any())->method('getId')->will($this->returnValue($sName));
        $oEvidence->expects($this->any())->method('getCountryId')->will($this->returnValue($sCountry));

        return $oEvidence;
    }
}
