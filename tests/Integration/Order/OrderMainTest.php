<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Order;

use PHPUnit\Framework\TestCase;

/**
 * Testing admin controller class.
 *
 * @covers oeVATTBEOrder_Main
 */
class OrderMainTest extends TestCase
{
    /**
     * Creates dummy order and checks country was set.
     *
     * @return array
     */
    public function testTBECountryTitle()
    {
        $this->_createOrder();

        /** @var oeVATTBEOrder_Main|Order_Main $oOrderMain */
        $oOrderMain = oxNew('Order_Main');
        $oOrderMain->setEditObjectId('order_id');

        $oOrderMain->render();
        $aViewData = $oOrderMain->getViewData();

        $this->assertSame('Deutschland', $aViewData['sTBECountry']);

        return $aViewData;
    }

    /**
     * Checks if view data is formed correctly.
     *
     * @param array $aViewData View data which is given to template.
     *
     * @depends testTBECountryTitle
     */
    public function testTBEEvidenceData($aViewData)
    {
        $aEvidenceData = $aViewData['aEvidencesData'];
        $aExpectedResult = array(
            'billing_country' => array(
                'name' => 'billing_country',
                'countryId' => 'a7c40f631fc920687.20179984',
                'timestamp' => $aEvidenceData['billing_country']['timestamp'],
                'countryTitle' => 'Deutschland'
            ),
            'geo_location' => array(
                'name' => 'geo_location',
                'countryId' => '',
                'timestamp' => $aEvidenceData['geo_location']['timestamp'],
                'countryTitle' => '-'
            )
        );
        $this->assertSame($aEvidenceData, $aExpectedResult);
    }

    /**
     * Creates dummy order.
     */
    private function _createOrder()
    {
        /** @var oeVATTBEOxBasket|oxBasket|PHPUnit_Framework_MockObject_MockObject $oBasket */
        $oBasket = $this->getMock('oeVATTBEOxBasket', array('hasOeTBEVATArticles'));
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));
        /** @var oeVATTBEOxUser|oxUser $oUser */
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxcountryid = new oxField('a7c40f631fc920687.20179984');
        $oUser->save();

        /** @var oeVATTBEOxOrder|oxOrder|PHPUnit_Framework_MockObject_MockObject $oOrder */
        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getFinalizeOrderParent"));
        $oOrder->expects($this->any())->method("_getFinalizeOrderParent")->will($this->returnValue(oxOrder::ORDER_STATE_OK));

        $oOrder->setId('order_id');
        $oOrder->finalizeOrder($oBasket, $oUser, false);
        $oOrder->oxorder__oevattbe_evidenceused = new oxField('billing_country');
        $oOrder->oxorder__oevattbe_countryid = new oxField('a7c40f631fc920687.20179984');
        $oOrder->save();
    }
}
