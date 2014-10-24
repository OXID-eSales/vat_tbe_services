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
 * Testing admin controller class.
 *
 * @covers oeVATTBEOrder_Main
 */
class Integration_oeVatTbe_OrderEvidenceList_oeVATTBEOrder_MainTest extends OxidTestCase
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
        $oOrderMain = oxNew('oeVATTBEOrder_Main');
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
        /** @var oeVATTBEOxBasket|oxBasket $oBasket */
        $oBasket = oxNew('oeVATTBEOxBasket');
        /** @var oeVATTBEOxUser|oxUser $oUser */
        $oUser = oxNew('oeVATTBEOxUser');
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
