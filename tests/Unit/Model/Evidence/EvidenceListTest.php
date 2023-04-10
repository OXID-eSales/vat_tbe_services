<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model\Evidence;

use PHPUnit\Framework\TestCase;

/**
 * Test class for oeVATTBEEvidenceCalculator.
 *
 * @covers EvidenceList
 */
class EvidenceListTest extends TestCase
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

        $this->expectException('oxException');

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
        $oEvidence = $this->getMock('oeVATTBEEvidence', array('getId', 'getCountryId'), array(), '', false);
        $oEvidence->expects($this->any())->method('getId')->will($this->returnValue($sName));
        $oEvidence->expects($this->any())->method('getCountryId')->will($this->returnValue($sCountry));

        return $oEvidence;
    }
}
