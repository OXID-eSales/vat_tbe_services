<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
        $oBasket = $this->getMock('oeVATTBEOxBasket', array('hasOeTBEVATArticles'));
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));
        /** @var oeVATTBEOxUser|oxUser $oUser */
        $oUser = oxNew('oxUser');

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
    }

    /**
     * Order was successful;
     * Order was not recalculating;
     * Evidence used should be saved to database.
     */
    public function testSavingEvidenceUsedSavedOnFinalizeOrder()
    {
        /** @var oxBasket| $oBasket */
        $oBasket = oxNew('oxBasket');

        /** @var oxUser|PHPUnit_Framework_MockObject_MockObject $oUser */
        $oUser = $this->getMock('oeVATTBEOxUser', array('getOeVATTBETbeEvidenceUsed'));
        $oUser->expects($this->any())->method('getOeVATTBETbeEvidenceUsed')->will($this->returnValue('billing_country'));

        /** @var oeVATTBEOxOrder|oxOrder|PHPUnit_Framework_MockObject_MockObject $oOrder */
        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getFinalizeOrderParent"));
        $oOrder->expects($this->any())->method("_getFinalizeOrderParent")->will($this->returnValue(oxOrder::ORDER_STATE_PAYMENTERROR));

        $oOrder->setId('order_id');
        $oOrder->finalizeOrder($oBasket, $oUser, false);

        $this->assertEquals('billing_country', $oOrder->oxorder__oevattbe_evidenceused->value);
    }

    /**
     * Order was successful;
     * Order was recalculating;
     * Evidence used should not be changed.
     */
    public function testEvidenceUsedNotChangedOnOrderRecalculation()
    {
        /** @var oxBasket| $oBasket */
        $oBasket = oxNew('oxBasket');

        /** @var oxUser|PHPUnit_Framework_MockObject_MockObject $oUser */
        $oUser = $this->getMock('oeVATTBEOxUser', array('getOeVATTBETbeEvidenceUsed'));
        $oUser->expects($this->any())->method('getOeVATTBETbeEvidenceUsed')->will($this->returnValue('geo_location'));

        /** @var oeVATTBEOxOrder|oxOrder|PHPUnit_Framework_MockObject_MockObject $oOrder */
        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getFinalizeOrderParent"));
        $oOrder->expects($this->any())->method("_getFinalizeOrderParent")->will($this->returnValue(oxOrder::ORDER_STATE_PAYMENTERROR));
        $oOrder->oxorder__oevattbe_evidenceused = new oxField('billing_country');

        $oOrder->setId('order_id');
        $oOrder->finalizeOrder($oBasket, $oUser, true);

        $this->assertEquals('billing_country', $oOrder->oxorder__oevattbe_evidenceused->value);
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
        $oBasket = $this->getMock('oeVATTBEOxBasket', array('hasOeTBEVATArticles'));
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));
        /** @var oeVATTBEOxUser|oxUser $oUser */
        $oUser = oxNew('oxUser');

        /** @var oeVATTBEOxOrder|oxOrder|PHPUnit_Framework_MockObject_MockObject $oOrder */
        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getFinalizeOrderParent"));
        $oOrder->expects($this->any())->method("_getFinalizeOrderParent")->will($this->returnValue(oxOrder::ORDER_STATE_OK));

        $oOrder->setId('order_id');
        $oOrder->save();
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
        $oBasket = $this->getMock('oeVATTBEOxBasket', array('hasOeTBEVATArticles'));
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue($blHasTBEArticles));
        /** @var oeVATTBEOxUser|oxUser $oUser */
        $oUser = oxNew('oxUser');

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
    }
}
