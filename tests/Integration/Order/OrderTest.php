<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Order;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\DbGateway\OrderEvidenceListDbGateway;
use OxidEsales\EVatModule\Model\Evidence\Item\BillingCountryEvidence;
use OxidEsales\EVatModule\Model\OrderEvidenceList;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Shop\Basket;
use OxidEsales\EVatModule\Shop\Order;
use OxidEsales\EVatModule\Shop\User;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * Testing oeVATTBEOrder class.
 */
class OrderTest extends BaseTestCase
{
    use ServiceContainer;

    /**
     * Data provider for SavingEvidenceList test.
     *
     * @return array
     */
    public static function providerSavingEvidenceList()
    {
        return array(
            array(Order::ORDER_STATE_OK),
            array(Order::ORDER_STATE_MAILINGERROR)
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
        Registry::getSession()->destroy();

        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class]);
        $moduleSettings->saveCountryEvidences(['billing_country' => 1]);
        $moduleSettings->saveDefaultEvidence('billing_country');

        /** @var Basket $oBasket */
        $oBasket = $this->getMockBuilder(Basket::class)
                ->onlyMethods(array("hasOeTBEVATArticles"))
                ->getMock();
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));

        /** @var User $oUser */
        $oUser = oxNew(User::class);
        Registry::getSession()->setUser($oUser);

        /** @var Order $oOrder */
        $oOrder = $this->getMockBuilder(Order::class)
                ->onlyMethods(array("getFinalizeOrderParent"))
                ->getMock();
        $oOrder->expects($this->any())->method("getFinalizeOrderParent")->will($this->returnValue($iOrderState));

        $oOrder->setId('new_order_id');
        $oOrder->finalizeOrder($oBasket, $oUser, false);

        $oGateway = oxNew(OrderEvidenceListDbGateway::class);
        /** @var OrderEvidenceList $oList */
        $oList = oxNew(OrderEvidenceList::class, $oGateway);
        $oList->load('new_order_id');

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
        /** @var Basket| $oBasket */
        $oBasket = oxNew(Basket::class);

        /** @var User $oUser */
        $oUser = $this->getMockBuilder(User::class)
                ->onlyMethods(array("getOeVATTBETbeEvidenceUsed"))
                ->getMock();
        $oUser->expects($this->any())->method('getOeVATTBETbeEvidenceUsed')->will($this->returnValue('billing_country'));

        /** @var Order$oOrder */
        $oOrder = $this->getMockBuilder(Order::class)
                ->onlyMethods(array("getFinalizeOrderParent"))
                ->getMock();
        $oOrder->expects($this->any())->method("getFinalizeOrderParent")->will($this->returnValue(Order::ORDER_STATE_PAYMENTERROR));

        $oOrder->setId('new_order_id');
        $oOrder->finalizeOrder($oBasket, $oUser, false);

        $this->assertEquals('billing_country', $oOrder->getFieldData('oevattbe_evidenceused'));
    }

    /**
     * Order was successful;
     * Order was recalculating;
     * Evidence used should not be changed.
     */
    public function testEvidenceUsedNotChangedOnOrderRecalculation()
    {
        /** @var Basket| $oBasket */
        $oBasket = oxNew(Basket::class);

        /** @var User $oUser */
        $oUser = $this->getMockBuilder(User::class)
                ->onlyMethods(array("getOeVATTBETbeEvidenceUsed"))
                ->getMock();
        $oUser->expects($this->any())->method('getOeVATTBETbeEvidenceUsed')->will($this->returnValue('geo_location'));

        /** @var Order $oOrder */
        $oOrder = $this->getMockBuilder(Order::class)
                ->onlyMethods(array("getFinalizeOrderParent"))
                ->getMock();
        $oOrder->expects($this->any())->method("getFinalizeOrderParent")->will($this->returnValue(Order::ORDER_STATE_PAYMENTERROR));
        $oOrder->assign([
            'oevattbe_evidenceused' => 'billing_country'
        ]);

        $oOrder->setId('new_order_id');
        $oOrder->finalizeOrder($oBasket, $oUser, true);

        $this->assertEquals('billing_country', $oOrder->getFieldData('oevattbe_evidenceused'));
    }

    /**
     * Test deleting evidences list.
     */
    public function testDeletingEvidenceList()
    {
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class]);
        $moduleSettings->saveDefaultEvidence('billing_country');

        /** @var Basket $oBasket */
        $oBasket = $this->getMockBuilder(Basket::class)
                ->onlyMethods(array("hasOeTBEVATArticles"))
                ->getMock();
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));

        /** @var User $oUser */
        $oUser = oxNew(User::class);

        /** @var Order $oOrder */
        $oOrder = $this->getMockBuilder(Order::class)
                ->onlyMethods(array("getFinalizeOrderParent"))
                ->getMock();
        $oOrder->expects($this->any())->method("getFinalizeOrderParent")->will($this->returnValue(Order::ORDER_STATE_OK));

        $oOrder->setId('new_order_id');
        $oOrder->save();
        $oOrder->finalizeOrder($oBasket, $oUser, false);

        $oOrder->delete('new_order_id');

        $oGateway = oxNew(OrderEvidenceListDbGateway::class);
        /** @var OrderEvidenceList $oList */
        $oList = oxNew(OrderEvidenceList::class, $oGateway);
        $oList->load('new_order_id');

        $this->assertEquals(array(), $oList->getData());
    }

    /**
     * Data provider for NotSavingEvidenceListOnFailedOrder test.
     *
     * @return array
     */
    public static function providerNotSavingEvidenceListOnFailedOrder()
    {
        return array(
            array(Order::ORDER_STATE_OK, false),
            array(Order::ORDER_STATE_MAILINGERROR, false),
            array(Order::ORDER_STATE_PAYMENTERROR, true),
            array(Order::ORDER_STATE_ORDEREXISTS, true),
            array(Order::ORDER_STATE_INVALIDDELIVERY, true),
            array(Order::ORDER_STATE_INVALIDPAYMENT, true),
            array(Order::ORDER_STATE_INVALIDDELADDRESSCHANGED, true),
            array(Order::ORDER_STATE_BELOWMINPRICE, true),
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
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class]);
        $moduleSettings->saveDefaultEvidence('billing_country');

        /** @var Basket $oBasket */
        $oBasket = $this->getMockBuilder(Basket::class)
                ->onlyMethods(array("hasOeTBEVATArticles"))
                ->getMock();
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue($blHasTBEArticles));

        /** @var User $oUser */
        $oUser = oxNew(User::class);

        /** @var Order $oOrder */
        $oOrder = $this->getMockBuilder(Order::class)
                ->onlyMethods(array("getFinalizeOrderParent"))
                ->getMock();
        $oOrder->expects($this->any())->method("getFinalizeOrderParent")->will($this->returnValue($iOrderState));

        $oOrder->setId('new_order_id');
        $oOrder->finalizeOrder($oBasket, $oUser, false);

        $oGateway = oxNew(OrderEvidenceListDbGateway::class);
        /** @var OrderEvidenceList $oList */
        $oList = oxNew(OrderEvidenceList::class, $oGateway);
        $oList->load('new_order_id');

        $aData = $oList->getData();

        $this->assertEquals(array(), $aData);
    }
}
