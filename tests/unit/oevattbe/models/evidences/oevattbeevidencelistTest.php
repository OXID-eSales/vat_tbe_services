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
 * @covers oeVATTBEEvidenceList
 */
class Unit_oeVATTBE_Models_Evidences_oeVATTBEEvidenceListTest extends OxidTestCase
{
    public function testAddingToList()
    {
        /** @var oeVATTBEEvidence|PHPUnit_Framework_MockObject_MockObject $oEvidence */
        $oEvidence = $this->getMock('oeVATTBEEvidence', array(), array(), '', false);

        $oList = new oeVATTBEEvidenceList();
        $oList->add($oEvidence);

        $aElements = array();
        foreach ($oList as $iItem) {
            $aElements[] = $iItem;
        }

        $this->assertEquals(array($oEvidence), $aElements);
    }

    public function testAddingToListWhenNonEvidenceIsAdded()
    {
        $oList = new oeVATTBEEvidenceList();

        $this->setExpectedException('oxException');

        $oList->add(1);
    }

    public function testFormationOfArray()
    {
        $oBillingEvidence = $this->_createEvidence('billing_country', 'GermanyId');
        $oGeoEvidence = $this->_createEvidence('geo_location', 'LithuaniaId');

        $oList = new oeVATTBEEvidenceList(array($oBillingEvidence, $oGeoEvidence));

        $aExpectedArray = array(
            'billing_country' => array(
                'name' => 'billing_country',
                'countryId' => 'GermanyId'
            ),
            'geo_location' => array(
                'name' => 'geo_location',
                'countryId' => 'LithuaniaId'
            ),
        );

        $this->assertEquals($aExpectedArray, $oList->getArray());
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
