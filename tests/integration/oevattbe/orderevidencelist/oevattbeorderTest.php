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
     * Order was successfully;
     * Evidence list should be saved to database.
     *
     * @param int $iOrderState Order state when evidence list should be saved.
     *
     * @dataProvider providerSavingEvidenceList
     */
    public function testSavingEvidenceList($iOrderState)
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        /** @var oeVATTBEOxBasket|oxBasket|PHPUnit_Framework_MockObject_MockObject $oBasket */
        $oBasket = $this->getMock('oxBasket', array('hasVATTBEArticles'));
        $oBasket->expects($this->any())->method('hasVATTBEArticles')->will($this->returnValue(true));
        /** @var oeVATTBEOxUser|oxUser $oUser */
        $oUser = oxNew('oxUser');

        /** @var oeVATTBEOxOrder|oxOrder|PHPUnit_Framework_MockObject_MockObject $oOrder */
        $oOrder = $this->getMock("oxOrder", array("_getFinalizeOrderParent"));
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
    }

    /**
     * Test deleting evidences list.
     */
    public function testDeletingEvidenceList()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        /** @var oeVATTBEOxBasket|oxBasket|PHPUnit_Framework_MockObject_MockObject $oBasket */
        $oBasket = $this->getMock('oxBasket', array('hasVATTBEArticles'));
        $oBasket->expects($this->any())->method('hasVATTBEArticles')->will($this->returnValue(true));
        /** @var oeVATTBEOxUser|oxUser $oUser */
        $oUser = oxNew('oxUser');

        /** @var oeVATTBEOxOrder|oxOrder|PHPUnit_Framework_MockObject_MockObject $oOrder */
        $oOrder = $this->getMock("oxOrder", array("_getFinalizeOrderParent"));
        $oOrder->expects($this->any())->method("_getFinalizeOrderParent")->will($this->returnValue(oxOrder::ORDER_STATE_OK));

        $oOrder->setId('order_id');
        $oOrder->finalizeOrder($oBasket, $oUser, false);

        $oOrder->delete('order_id');

        $oGateway = oxNew('oeVATTBEOrderEvidenceListDbGateway');
        /** @var oeVATTBEOrderEvidenceList $oList */
        $oList = oxNew('oeVATTBEOrderEvidenceList', $oGateway);
        $oList->load('order_id');

        $this->assertEquals(array(), $oList->getData());
    }

    /**
     * Data provider for NotSavingEvidenceListOnFailedOrder test.
     *
     * @return array
     */
    public function providerNotSavingEvidenceListOnFailedOrder()
    {
        return array(
            array(oxOrder::ORDER_STATE_OK, false),
            array(oxOrder::ORDER_STATE_MAILINGERROR, false),
            array(oxOrder::ORDER_STATE_PAYMENTERROR, true),
            array(oxOrder::ORDER_STATE_ORDEREXISTS, true),
            array(oxOrder::ORDER_STATE_INVALIDDELIVERY, true),
            array(oxOrder::ORDER_STATE_INVALIDPAYMENT, true),
            array(oxOrder::ORDER_STATE_INVALIDTSPROTECTION, true),
            array(oxOrder::ORDER_STATE_INVALIDDElADDRESSCHANGED, true),
            array(oxOrder::ORDER_STATE_BELOWMINPRICE, true),
        );
    }

    /**
     * Order was not successfully;
     * Evidence list should not be saved to database.
     *
     * @param int  $iOrderState      Order state when evidence list should not be saved.
     * @param bool $blHasTBEArticles Order state when evidence list should not be saved.
     *
     * @dataProvider providerNotSavingEvidenceListOnFailedOrder
     */
    public function testNotSavingEvidenceListOnFailedOrder($iOrderState, $blHasTBEArticles)
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('aOeVATTBECountryEvidenceClasses', array('oeVATTBEBillingCountryEvidence'));
        $oConfig->setConfigParam('sOeVATTBEDefaultEvidence', 'billing_country');

        /** @var oeVATTBEOxBasket|oxBasket|PHPUnit_Framework_MockObject_MockObject $oBasket */
        $oBasket = $this->getMock('oxBasket', array('hasVATTBEArticles'));
        $oBasket->expects($this->any())->method('hasVATTBEArticles')->will($this->returnValue($blHasTBEArticles));
        /** @var oeVATTBEOxUser|oxUser $oUser */
        $oUser = oxNew('oxUser');

        /** @var oeVATTBEOxOrder|oxOrder|PHPUnit_Framework_MockObject_MockObject $oOrder */
        $oOrder = $this->getMock("oxOrder", array("_getFinalizeOrderParent"));
        $oOrder->expects($this->any())->method("_getFinalizeOrderParent")->will($this->returnValue($iOrderState));

        $oOrder->setId('order_id');
        $oOrder->finalizeOrder($oBasket, $oUser, false);

        $oGateway = oxNew('oeVATTBEOrderEvidenceListDbGateway');
        /** @var oeVATTBEOrderEvidenceList $oList */
        $oList = oxNew('oeVATTBEOrderEvidenceList', $oGateway);
        $oList->load('order_id');

        $aData = $oList->getData();

        $this->assertEquals(array(), $aData);
    }
}
