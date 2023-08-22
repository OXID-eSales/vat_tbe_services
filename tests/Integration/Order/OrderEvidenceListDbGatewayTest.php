<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Order;

use OxidEsales\EVatModule\Model\DbGateway\OrderEvidenceListDbGateway;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Test class for OrderEvidenceListDbGateway.
 */
class OrderEvidenceListDbGatewayTest extends BaseTestCase
{
    /**
     * Testing Order list saving to database. Test works with database so can be slow.
     *
     * @return OrderEvidenceListDbGateway
     */
    public function testOrderListSavingToDatabase()
    {
        $oOrderArticleList = oxNew(OrderEvidenceListDbGateway::class);
        $aData = array(
            'orderId' => 'order_id',
            'evidenceList' => array(
                'evidence1' => array(
                    'name' => 'evidence1',
                    'countryId' => 'GermanyId',
                ),
                'evidence2' => array(
                    'name' => 'evidence2',
                    'countryId' => 'GermanyId',
                )
            )
        );
        $this->assertNotSame(false, $oOrderArticleList->save($aData));

        return $oOrderArticleList;
    }

    /**
     * Testing Order list loading from database. Test works with database so can be slow.
     *
     * @param OrderEvidenceListDbGateway $oOrderArticleList
     *
     * @depends testOrderListSavingToDatabase
     *
     * @return OrderEvidenceListDbGateway
     */
    public function testOrderListLoading($oOrderArticleList)
    {
        $aData = $oOrderArticleList->load('order_id');

        $aExpectedData = array(
            'evidence1' => array(
                'name' => 'evidence1',
                'countryId' => 'GermanyId',
                'timestamp' => $aData['evidence1']['timestamp']
            ),
            'evidence2' => array(
                'name' => 'evidence2',
                'countryId' => 'GermanyId',
                'timestamp' => $aData['evidence2']['timestamp']
            )
        );

        $this->assertSame($aExpectedData, $oOrderArticleList->load('order_id'));

        return $oOrderArticleList;
    }

    /**
     * Testing deletion of Order list from database. Test works with database so can be slow.
     *
     * @param OrderEvidenceListDbGateway $oOrderArticleList
     *
     * @depends testOrderListLoading
     */
    public function testDeletingOrderList($oOrderArticleList)
    {
        $oOrderArticleList->delete('order_id');

        $this->assertSame(array(), $oOrderArticleList->load('order_id'));
    }

    /**
     * Test trying to save empty list to database.
     */
    public function testSavingEmptyList()
    {
        $oOrderArticleList = oxNew(OrderEvidenceListDbGateway::class);
        $aData = array(
            'orderId' => 'order_id',
            'evidenceList' => array()
        );
        $oOrderArticleList->save($aData);
        $this->assertSame(array(), $oOrderArticleList->load('order_id'));
    }

    /**
     * Test trying to load order, when no order exists in database.
     */
    public function testLoadingEmptyOrderList()
    {
        $oOrderArticleList = oxNew(OrderEvidenceListDbGateway::class);
        $this->assertSame(array(), $oOrderArticleList->load('non_existing_order'));
    }

    /**
     * Test deleting non existing order.
     */
    public function testDeletingEmptyOrderList()
    {
        $oOrderArticleList = oxNew(OrderEvidenceListDbGateway::class);
        $this->assertNotSame(false, $oOrderArticleList->delete('non_existing_order'));
    }
}
