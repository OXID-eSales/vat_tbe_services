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

declare(strict_types=1);

use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Testing oeVATTBEOrderEvidenceList class.
 */
class Integration_oeVatTbe_OrderEvidenceList_oeVATTBEOrderEvidenceListTest extends UnitTestCase
{
    private $orderEvidenceList;

    protected function setUp(): void
    {
        parent::setUp();

        $aData = [
            'evidence1' => [
                'name' => 'evidence1',
                'countryId' => 'a7c40f631fc920687.20179984',
            ],
            'evidence2' => [
                'name' => 'evidence2',
                'countryId' => 'NonExisting',
            ],
        ];

        $oGateway = oxNew('oeVATTBEOrderEvidenceListDbGateway');
        $this->orderEvidenceList = oxNew('oeVATTBEOrderEvidenceList', $oGateway);
        $this->orderEvidenceList->setId('order_id');
        $this->orderEvidenceList->setData($aData);
        $this->orderEvidenceList->save();
    }

    /**
     * Checks evidence list load.
     */
    public function testLoadingEvidenceList()
    {
        $oGateway = oxNew('oeVATTBEOrderEvidenceListDbGateway');

        /** @var oeVATTBEOrderEvidenceList $oList */
        $oList = oxNew('oeVATTBEOrderEvidenceList', $oGateway);
        $oList->load('order_id');

        $aData = $oList->getData();

        $aExpectedData = array(
            'evidence1' => array(
                'name' => 'evidence1',
                'countryId' => 'a7c40f631fc920687.20179984',
                'timestamp' => $aData['evidence1']['timestamp'],
            ),
            'evidence2' => array(
                'name' => 'evidence2',
                'countryId' => 'NonExisting',
                'timestamp' => $aData['evidence2']['timestamp'],
            ),
        );

        $this->assertEquals($aExpectedData, $aData);
    }

    /**
     * Loads with country names and checks.
     */
    public function testLoadWithCountryNamesEvidenceList()
    {
        $oGateway = oxNew('oeVATTBEOrderEvidenceListDbGateway');

        /** @var oeVATTBEOrderEvidenceList $oList */
        $oList = oxNew('oeVATTBEOrderEvidenceList', $oGateway);
        $oList->loadWithCountryNames('order_id');

        $aData = $oList->getData();

        $aExpectedData = array(
            'evidence1' => array(
                'name' => 'evidence1',
                'countryId' => 'a7c40f631fc920687.20179984',
                'timestamp' => $aData['evidence1']['timestamp'],
                'countryTitle' => 'Deutschland',
            ),
            'evidence2' => array(
                'name' => 'evidence2',
                'countryId' => 'NonExisting',
                'timestamp' => $aData['evidence2']['timestamp'],
                'countryTitle' => '-',
            ),
        );

        $this->assertEquals($aExpectedData, $aData);

        return $oList;
    }

    /**
     * Checks if deletes.
     */
    public function testDeletingEvidenceList()
    {
        $this->orderEvidenceList->delete();
        $this->orderEvidenceList->load();
        $this->assertEmpty($this->orderEvidenceList->getData());
    }
}
