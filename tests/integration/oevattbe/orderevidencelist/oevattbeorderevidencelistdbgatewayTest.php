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
 * Test class for oeVATTBEOrderEvidenceListDbGateway.
 *
 * @covers oeVATTBEOrderEvidenceListDbGateway
 */
class Integration_oeVatTbe_OrderEvidenceList_oeVATTBEOrderEvidenceListDbGatewayTest extends OxidTestCase
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
