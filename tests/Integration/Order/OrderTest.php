<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Order;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EVatModule\Model\DbGateway\OrderEvidenceListDbGateway;
use OxidEsales\EVatModule\Model\Evidence\Item\BillingCountryEvidence;
use OxidEsales\EVatModule\Model\OrderEvidenceList;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Shop\Basket;
use OxidEsales\EVatModule\Shop\Order;
use OxidEsales\EVatModule\Shop\User;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Testing oeVATTBEOrder class.
 */
#[AllowMockObjectsWithoutExpectations]
class OrderTest extends BaseTestCase
{
    /**
     * Data provider for SavingEvidenceList test.
     *
     * @return array
     */
    public static function providerSavingEvidenceList()
    {
        return [
            [Order::ORDER_STATE_OK],
            [Order::ORDER_STATE_MAILINGERROR]
        ];
    }

    /**
     * Order was successfully;
     * Evidence list should be saved to database.
     *
     * @param int $iOrderState Order state when evidence list should be saved.
     */
    #[DataProvider('providerSavingEvidenceList')]
    public function testSavingEvidenceList($iOrderState)
    {
        $oSession = Registry::getSession();
        $oSession->setVariable('TBECountryId', null);

        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class]);
        $moduleSettings->saveCountryEvidences(['billing_country' => 1]);
        $moduleSettings->saveDefaultEvidence('billing_country');

        /** @var Basket $oBasket */
        $oBasket = $this->getMockBuilder(Basket::class)
                ->onlyMethods(["hasOeTBEVATArticles"])
                ->getMock();
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->willReturn(true);

        /** @var User $oUser */
        $oUser = oxNew(User::class);
        Registry::getSession()->setUser($oUser);

        /** @var Order $oOrder */
        $oOrder = $this->getMockBuilder(Order::class)
                ->onlyMethods(["getFinalizeOrderParent"])
                ->getMock();
        $oOrder->expects($this->any())->method("getFinalizeOrderParent")->willReturn($iOrderState);

        $oOrder->setId('new_order_id');
        $oOrder->finalizeOrder($oBasket, $oUser, false);

        $oGateway = oxNew(OrderEvidenceListDbGateway::class);
        /** @var OrderEvidenceList $oList */
        $oList = oxNew(OrderEvidenceList::class, $oGateway);
        $oList->load('new_order_id');

        $aData = $oList->getData();

        $aExpectedData = [
            'billing_country' => [
                'name' => 'billing_country',
                'countryId' => '',
                'timestamp' => $aData['billing_country']['timestamp']
            ]
        ];

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
                ->onlyMethods(["getOeVATTBETbeEvidenceUsed"])
                ->getMock();
        $oUser->expects($this->any())->method('getOeVATTBETbeEvidenceUsed')->willReturn('billing_country');

        /** @var Order$oOrder */
        $oOrder = $this->getMockBuilder(Order::class)
                ->onlyMethods(["getFinalizeOrderParent"])
                ->getMock();
        $oOrder->expects($this->any())->method("getFinalizeOrderParent")->willReturn(Order::ORDER_STATE_PAYMENTERROR);

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
                ->onlyMethods(["getOeVATTBETbeEvidenceUsed"])
                ->getMock();
        $oUser->expects($this->any())->method('getOeVATTBETbeEvidenceUsed')->willReturn('geo_location');

        /** @var Order $oOrder */
        $oOrder = $this->getMockBuilder(Order::class)
                ->onlyMethods(["getFinalizeOrderParent"])
                ->getMock();
        $oOrder->expects($this->any())->method("getFinalizeOrderParent")->willReturn(Order::ORDER_STATE_PAYMENTERROR);
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
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class]);
        $moduleSettings->saveDefaultEvidence('billing_country');

        /** @var Basket $oBasket */
        $oBasket = $this->getMockBuilder(Basket::class)
                ->onlyMethods(["hasOeTBEVATArticles"])
                ->getMock();
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->willReturn(true);

        /** @var User $oUser */
        $oUser = oxNew(User::class);

        /** @var Order $oOrder */
        $oOrder = $this->getMockBuilder(Order::class)
                ->onlyMethods(["getFinalizeOrderParent"])
                ->getMock();
        $oOrder->expects($this->any())->method("getFinalizeOrderParent")->willReturn(Order::ORDER_STATE_OK);

        $oOrder->setId('new_order_id');
        $oOrder->save();
        $oOrder->finalizeOrder($oBasket, $oUser, false);

        $oOrder->delete('new_order_id');

        $oGateway = oxNew(OrderEvidenceListDbGateway::class);
        /** @var OrderEvidenceList $oList */
        $oList = oxNew(OrderEvidenceList::class, $oGateway);
        $oList->load('new_order_id');

        $this->assertEquals([], $oList->getData());
    }

    /**
     * Data provider for NotSavingEvidenceListOnFailedOrder test.
     *
     * @return array
     */
    public static function providerNotSavingEvidenceListOnFailedOrder()
    {
        return [
            [Order::ORDER_STATE_OK, false],
            [Order::ORDER_STATE_MAILINGERROR, false],
            [Order::ORDER_STATE_PAYMENTERROR, true],
            [Order::ORDER_STATE_ORDEREXISTS, true],
            [Order::ORDER_STATE_INVALIDDELIVERY, true],
            [Order::ORDER_STATE_INVALIDPAYMENT, true],
            [Order::ORDER_STATE_INVALIDDELADDRESSCHANGED, true],
            [Order::ORDER_STATE_BELOWMINPRICE, true],
        ];
    }

    /**
     * Order was not successfully;
     * Evidence list should not be saved to database.
     *
     * @param int  $iOrderState      Order state when evidence list should not be saved.
     * @param bool $blHasTBEArticles Order state when evidence list should not be saved.
     */
    #[DataProvider('providerNotSavingEvidenceListOnFailedOrder')]
    public function testNotSavingEvidenceListOnFailedOrder($iOrderState, $blHasTBEArticles)
    {
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class]);
        $moduleSettings->saveDefaultEvidence('billing_country');

        /** @var Basket $oBasket */
        $oBasket = $this->getMockBuilder(Basket::class)
                ->onlyMethods(["hasOeTBEVATArticles"])
                ->getMock();
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->willReturn($blHasTBEArticles);

        /** @var User $oUser */
        $oUser = oxNew(User::class);

        /** @var Order $oOrder */
        $oOrder = $this->getMockBuilder(Order::class)
                ->onlyMethods(["getFinalizeOrderParent"])
                ->getMock();
        $oOrder->expects($this->any())->method("getFinalizeOrderParent")->willReturn($iOrderState);

        $oOrder->setId('new_order_id');
        $oOrder->finalizeOrder($oBasket, $oUser, false);

        $oGateway = oxNew(OrderEvidenceListDbGateway::class);
        /** @var OrderEvidenceList $oList */
        $oList = oxNew(OrderEvidenceList::class, $oGateway);
        $oList->load('new_order_id');

        $aData = $oList->getData();

        $this->assertEquals([], $aData);
    }
}
