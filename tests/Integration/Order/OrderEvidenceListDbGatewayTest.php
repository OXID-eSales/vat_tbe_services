<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Order;

use PHPUnit\Framework\TestCase;

/**
 * Test class for oeVATTBEOrderEvidenceListDbGateway.
 *
 * @covers oeVATTBEOrderEvidenceListDbGateway
 */
class OrderEvidenceListDbGatewayTest extends TestCase
{

    /**
     * Testing Order list saving to database. Test works with database so can be slow.
     *
     * @return oeVATTBEOrderEvidenceListDbGateway
     */
    public function testOrderListSavingToDatabase()
    {
        $oOrderArticleList = oxNew('oeVATTBEOrderEvidenceListDbGateway');
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
     * @param oeVATTBEOrderEvidenceListDbGateway $oOrderArticleList
     *
     * @depends testOrderListSavingToDatabase
     *
     * @return oeVATTBEOrderEvidenceListDbGateway
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
     * @param oeVATTBEOrderEvidenceListDbGateway $oOrderArticleList
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
        $oOrderArticleList = oxNew('oeVATTBEOrderEvidenceListDbGateway');
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
        $oOrderArticleList = oxNew('oeVATTBEOrderEvidenceListDbGateway');
        $this->assertSame(array(), $oOrderArticleList->load('non_existing_order'));
    }

    /**
     * Test deleting non existing order.
     */
    public function testDeletingEmptyOrderList()
    {
        $oOrderArticleList = oxNew('oeVATTBEOrderEvidenceListDbGateway');
        $this->assertNotSame(false, $oOrderArticleList->delete('non_existing_order'));
    }
}
