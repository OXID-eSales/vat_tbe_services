<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Order;

use OxidEsales\EVatModule\Model\DbGateway\OrderEvidenceListDbGateway;
use OxidEsales\EVatModule\Model\OrderEvidenceList;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Testing oeVATTBEOrderEvidenceList class.
 */
class OrderEvidenceListTest extends BaseTestCase
{
    private $orderEvidenceList;

    public function setUp(): void
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

        $oGateway = oxNew(OrderEvidenceListDbGateway::class);
        $this->orderEvidenceList = oxNew(OrderEvidenceList::class, $oGateway);
        $this->orderEvidenceList->setId('order_id');
        $this->orderEvidenceList->setData($aData);
        $this->orderEvidenceList->save();
    }

    /**
     * Checks evidence list load.
     */
    public function testLoadingEvidenceList()
    {
        $oGateway = oxNew(OrderEvidenceListDbGateway::class);

        /** @var OrderEvidenceList $oList */
        $oList = oxNew(OrderEvidenceList::class, $oGateway);
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
        $oGateway = oxNew(OrderEvidenceListDbGateway::class);

        /** @var OrderEvidenceList $oList */
        $oList = oxNew(OrderEvidenceList::class, $oGateway);
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
