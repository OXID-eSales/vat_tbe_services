<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Order;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EVatModule\Controller\Admin\OrderMain;
use OxidEsales\EVatModule\Model\Evidence\Item\BillingCountryEvidence;
use OxidEsales\EVatModule\Model\Evidence\Item\GeoLocationEvidence;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Shop\Basket;
use OxidEsales\EVatModule\Shop\Order;
use OxidEsales\EVatModule\Shop\User;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Testing admin controller class.
 */
class OrderMainTest extends BaseTestCase
{
    /**
     * Creates dummy order and checks country was set.
     *
     * @return array
     */
    public function testTBECountryTitle()
    {
        $moduleSettings = ContainerFacade::get(ModuleSettings::class);
        $moduleSettings->saveEvidenceClasses([BillingCountryEvidence::class, GeoLocationEvidence::class]);
        $moduleSettings->saveCountryEvidences(['billing_country' => 1, 'geo_location' => 1]);
        $moduleSettings->saveDefaultEvidence('billing_country');

        $this->createOrder();

        /** @var OrderMain $oOrderMain */
        $oOrderMain = oxNew(OrderMain::class);
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
    private function createOrder()
    {
        /** @var Basket $oBasket */
        $oBasket = $this->getMockBuilder(Basket::class)
            ->onlyMethods(array("hasOeTBEVATArticles"))
            ->getMock();
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));
        /** @var User $oUser */
        $oUser = oxNew(User::class);
        $oUser->assign([
            'oxusername'  => '',
            'oxpassword'  => '',
            'oxregister'  => '',
            'oxcountryid' => 'a7c40f631fc920687.20179984',
            'oxboni'      => ''
        ]);
        $oUser->save();
        Registry::getSession()->setUser($oUser);

        /** @var Order $oOrder */
        $oOrder = $this->getMockBuilder(Order::class)
            ->onlyMethods(array("getFinalizeOrderParent"))
            ->getMock();
        $oOrder->expects($this->any())->method("getFinalizeOrderParent")->will($this->returnValue(Order::ORDER_STATE_OK));

        $oOrder->setId('order_id');
        $oOrder->finalizeOrder($oBasket, $oUser, false);
        $oOrder->assign([
            'oxcurrency'            => 'EUR',
            'oxorderdate'           => '',
            'oxsenddate'            => '',
            'oevattbe_evidenceused' => 'billing_country',
            'OEVATTBE_COUNTRYID'    => 'a7c40f631fc920687.20179984',
        ]);
        $oOrder->save();
    }
}
