<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Model\Evidence;

use OxidEsales\EVatModule\Model\Evidence\EvidenceList;
use OxidEsales\EVatModule\Model\Evidence\Item\Evidence;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for oeVATTBEEvidenceCalculator.
 */
class EvidenceListTest extends TestCase
{
    public function testAddingToList()
    {
        /** @var Evidence|MockObject $oEvidence */
        $oEvidence = $this->createMock(Evidence::class);

        $oList = new EvidenceList();
        $oList->add($oEvidence);

        $aElements = [];
        foreach ($oList as $iItem) {
            $aElements[] = $iItem;
        }

        $this->assertEquals([$oEvidence], $aElements);
    }

    public function testAddingToListWhenNonEvidenceIsAdded()
    {
        $oList = new EvidenceList();

        $this->expectException('oxException');

        $oList->add(1);
    }

    public function testFormationOfArray()
    {
        $oBillingEvidence = $this->_createEvidence('billing_country', 'GermanyId');
        $oGeoEvidence = $this->_createEvidence('geo_location', 'LithuaniaId');

        $oList = new EvidenceList([$oBillingEvidence, $oGeoEvidence]);

        $aExpectedArray = [
            'billing_country' => [
                'name'      => 'billing_country',
                'countryId' => 'GermanyId'
            ],
            'geo_location'    => [
                'name'      => 'geo_location',
                'countryId' => 'LithuaniaId'
            ],
        ];

        $this->assertEquals($aExpectedArray, $oList->getArray());
    }


    /**
     * Creates evidence object with given name and country.
     *
     * @param string $sName
     * @param string $sCountry
     *
     * @return Evidence
     */
    protected function _createEvidence($sName, $sCountry)
    {
        /** @var Evidence|MockObject $oUser */
        $oEvidence = $this->createPartialMock(Evidence::class, ['getId', 'getCountryId']);
        $oEvidence->expects($this->any())->method('getId')->will($this->returnValue($sName));
        $oEvidence->expects($this->any())->method('getCountryId')->will($this->returnValue($sCountry));

        return $oEvidence;
    }
}
