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
 * @copyright (C) OXID eSales AG 2003-2014
 */


/**
 * Testing oeVATTBEoxOrder class.
 */
class Integration_oeVatTbe_OrderEvidenceList_oeVATTBEOrderTest extends OxidTestCase
{

    /**
     * Data provider for SavingEvidenceList test.
     *
     * @return array
     */
    public function providerSavingEvidenceList()
    {
        return array(
            array(oxOrder::ORDER_STATE_OK),
            array(oxOrder::ORDER_STATE_MAILINGERROR)
        );
    }

    /**
     * Order was made successfully;
     * Order status: OK;
     * Evidence list should be saved to database.
     *
     * @param int $iOrderState Order state when evidence list should be saved.
     *
     * @dataProvider providerSavingEvidenceList
     *
     * @return oeVATTBEOrderEvidenceList
     */
    public function testSavingEvidenceList($iOrderState)
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        /** @var oeVATTBEOxBasket|oxBasket $oBasket */
        $oBasket = oxNew('oeVATTBEOxBasket');
        /** @var oeVATTBEOxUser|oxUser $oUser */
        $oUser = oxNew('oeVATTBEOxUser');

        /** @var oeVATTBEOxOrder|oxOrder|PHPUnit_Framework_MockObject_MockObject $oOrder */
        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getFinalizeOrderParent"));
        $oOrder->expects($this->any())->method("_getFinalizeOrderParent")->will($this->returnValue($iOrderState));

        $oOrder->setId('order_id');
        $oOrder->finalizeOrder($oBasket, $oUser, false);

        $oGateway = oxNew('oeVATTBEOrderEvidenceListDbGateway');
        /** @var oeVATTBEOrderEvidenceList $oList */
        $oList = oxNew('oeVATTBEOrderEvidenceList', $oGateway);
        $oList->load('order_id');

        $aData = $oList->getData();

        $aExpectedData = array(
            'billing_country' => array(
                'name' => 'billing_country',
                'countryId' => '',
                'timestamp' => $aData['billing_country']['timestamp']
            )
        );

        $this->assertEquals($aExpectedData, $aData);

        return $oOrder;
    }

    public function testDeletingEvidenceList()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        /** @var oeVATTBEOxBasket|oxBasket $oBasket */
        $oBasket = oxNew('oeVATTBEOxBasket');
        /** @var oeVATTBEOxUser|oxUser $oUser */
        $oUser = oxNew('oeVATTBEOxUser');

        /** @var oeVATTBEOxOrder|oxOrder|PHPUnit_Framework_MockObject_MockObject $oOrder */
        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getFinalizeOrderParent"));
        $oOrder->expects($this->any())->method("_getFinalizeOrderParent")->will($this->returnValue(oxOrder::ORDER_STATE_OK));

        $oOrder->setId('order_id');
        $oOrder->finalizeOrder($oBasket, $oUser, false);

        $oOrder->delete('order_id');

        $oGateway = oxNew('oeVATTBEOrderEvidenceListDbGateway');
        /** @var oeVATTBEOrderEvidenceList $oList */
        $oList = oxNew('oeVATTBEOrderEvidenceList', $oGateway);
        $oList->load('order_id');

        $aData = $oList->getData();

        $this->assertEquals(array(), $aData);
    }

    /**
     * Data provider for SavingEvidenceList test.
     *
     * @return array
     */
    public function providerNotSavingEvidenceListOnFailedOrder()
    {
        return array(
            array(oxOrder::ORDER_STATE_PAYMENTERROR),
            array(oxOrder::ORDER_STATE_ORDEREXISTS),
            array(oxOrder::ORDER_STATE_INVALIDDELIVERY),
            array(oxOrder::ORDER_STATE_INVALIDPAYMENT),
            array(oxOrder::ORDER_STATE_INVALIDTSPROTECTION),
            array(oxOrder::ORDER_STATE_INVALIDDElADDRESSCHANGED),
            array(oxOrder::ORDER_STATE_BELOWMINPRICE),
        );
    }

    /**
     * Order was made successfully;
     * Order status: OK;
     * Evidence list should be saved to database.
     *
     * @param int $iOrderState Order state when evidence list should be saved.
     *
     * @dataProvider providerNotSavingEvidenceListOnFailedOrder
     *
     * @return oeVATTBEOrderEvidenceList
     */
    public function testNotSavingEvidenceListOnFailedOrder($iOrderState)
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        /** @var oeVATTBEOxBasket|oxBasket $oBasket */
        $oBasket = oxNew('oeVATTBEOxBasket');
        /** @var oeVATTBEOxUser|oxUser $oUser */
        $oUser = oxNew('oeVATTBEOxUser');

        /** @var oeVATTBEOxOrder|oxOrder|PHPUnit_Framework_MockObject_MockObject $oOrder */
        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getFinalizeOrderParent"));
        $oOrder->expects($this->any())->method("_getFinalizeOrderParent")->will($this->returnValue($iOrderState));

        $oOrder->setId('order_id');
        $oOrder->finalizeOrder($oBasket, $oUser, false);

        $oGateway = oxNew('oeVATTBEOrderEvidenceListDbGateway');
        /** @var oeVATTBEOrderEvidenceList $oList */
        $oList = oxNew('oeVATTBEOrderEvidenceList', $oGateway);
        $oList->load('order_id');

        $aData = $oList->getData();

        $this->assertEquals(array(), $aData);

        return $oOrder;
    }
}
