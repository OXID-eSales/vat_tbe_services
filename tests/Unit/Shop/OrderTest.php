<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Shop;

use OxidEsales\Eshop\Application\Model\Order as EShopOrder;
use OxidEsales\Eshop\Application\Model\User as EShopUser;
use OxidEsales\EVatModule\Model\User as UserModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\OrderArticleChecker;
use OxidEsales\EVatModule\Model\OrderEvidenceList;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\Eshop\Application\Model\Article as EShopArticle;
use OxidEsales\EVatModule\Shop\Basket;
use OxidEsales\Eshop\Application\Model\Basket as EShopBasket;
use OxidEsales\EVatModule\Shop\Country;
use OxidEsales\EVatModule\Shop\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use OxidEsales\EVatModule\Shop\Order;

/**
 * Testing extended oxArticle class.
 *
 * @covers Order
 */
class OrderTest extends TestCase
{
    /**
     * When user and basket countries does not match error code should be returned.
     */
    public function testValidateOrderWhenUserDoesNotMatchBasketAddress()
    {
        $oBasket = $this->createPartialMock(Basket::class, ["getOeVATTBETbeCountryId"]);
        $oBasket->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue('LithuaniaId'));
        Registry::getSession()->setVariable('TBECountryId', 'GermanyId');

        /** @var EShopUser|User $oUser */
        $oUser = oxNew(EShopUser::class);

        $oOrder = $this->createPartialMock(Order::class, ["getValidateOrderParent"]);
        $oOrder->expects($this->any())->method("getValidateOrderParent")->will($this->returnValue(0));

        $this->assertSame(EShopOrder::ORDER_STATE_INVALIDDELADDRESSCHANGED, $oOrder->validateOrder($oBasket, $oUser));
    }

    /**
     * When user and basket countries does not match but order already had errors, error code should not be changed.
     */
    public function testValidateOrderWhenUserDoesNotMatchBasketAddressAndOrderHadError()
    {
        $oBasket = $this->createPartialMock(Basket::class, ["getOeVATTBETbeCountryId"]);
        $oBasket->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue('LithuaniaId'));
        Registry::getSession()->setVariable('TBECountryId', 'GermanyId');

        /** @var EShopUser|User $oUser */
        $oUser = oxNew(EShopUser::class);

        $oOrder = $this->createPartialMock(Order::class, ["getValidateOrderParent"]);
        $oOrder->expects($this->any())->method("getValidateOrderParent")->will($this->returnValue(Order::ORDER_STATE_PAYMENTERROR));

        $this->assertSame(EShopOrder::ORDER_STATE_PAYMENTERROR, $oOrder->validateOrder($oBasket, $oUser));
    }

    /**
     * provider for testValidateOrderWithInvalidArticles
     *
     * @return array
     */
    public function providerValidateOrderWithInvalidArticles(): array
    {
        return [
            ['a7c40f6320aeb2ec2.72885259', false, true],
            ['NonExistingCountry', true, false],
            ['', true, false],
        ];
    }

    /**
     * Test order validation when order should be invalid because of invalid articles.
     *
     * @param string $sUserCountryId    User country id.
     * @param bool   $blValidArticles Whether basket articles are valid.
     * @param bool   $validCountry Whether user country is valid.
     *
     * @dataProvider providerValidateOrderWithInvalidArticles
     */
    public function testValidateOrderWithInvalidArticles($sUserCountryId, $blValidArticles, $validCountry)
    {
        /** @var Article|EShopArticle|MockObject $oArticle */
        $oArticle = $this->createPartialMock(Article::class, ["getOeVATTBETBEVat", "isOeVATTBETBEService"]);
        $oArticle->expects($this->any())->method("isOeVATTBETBEService")->will($this->returnValue(true));
        $oArticle->expects($this->any())->method("getOeVATTBETBEVat")->will($this->returnValue($blValidArticles ? 19 : null));

        /** @var Basket|EShopBasket|MockObject $oArticle */
        $oBasket = $this->createPartialMock(Basket::class, ["getOeVATTBETbeCountryId", "hasOeTBEVATArticles", "getBasketArticles"]);
        $oBasket->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue($sUserCountryId));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getBasketArticles")->will($this->returnValue([$oArticle]));
        Registry::getSession()->setBasket($oBasket);
        Registry::getSession()->setVariable('TBECountryId', $sUserCountryId);

        $country = null;
        if ($validCountry) {
            $country = $this->createPartialMock(Country::class, ['isInEU', 'appliesOeTBEVATTbeVat']);
            $country->method('isInEU')->willReturn(true);
            $country->method('appliesOeTBEVATTbeVat')->willReturn(true);
        }

        $userModel = $this->createPartialMock(UserModel::class, ['getCountry']);
        $userModel->method('getCountry')->willReturn($country);
        $articleChecker = oxNew(OrderArticleChecker::class, $userModel);

        $oOrder = $this->createPartialMock(Order::class, ["getValidateOrderParent", "getOeVATTBEOrderArticleChecker"]);
        $oOrder->expects($this->any())->method("getValidateOrderParent")->will($this->returnValue(0));
        $oOrder->method('getOeVATTBEOrderArticleChecker')->willReturn($articleChecker);

        $this->assertSame(Order::ORDER_STATE_TBE_NOT_CONFIGURED, $oOrder->validateOrder($oBasket, $userModel));
    }

    /**
     * provider for testValidateOrderWithValidArticles
     *
     * @return array
     */
    public function providerValidateOrderWithValidArticles(): array
    {
        // Domestic country.
        $sGermanyId = 'a7c40f631fc920687.20179984';

        // Non domestic non EU country.
        $sAustraliaId = '8f241f11095410f38.37165361';

        return [
            [$sGermanyId, true, true],
            [$sGermanyId, false, true],
            [$sGermanyId, false, false],
            [$sAustraliaId, true, true],
            [$sAustraliaId, false, true],
            [$sAustraliaId, false, false],
        ];
    }

    /**
     * Test order validation when order should be invalid because of invalid articles.
     *
     * @param string $sUserCountry     User country.
     * @param bool   $blHasTBEArticles Whether basket has tbe articles.
     * @param bool   $blValidArticles  Whether basket articles are valid.
     *
     * @dataProvider providerValidateOrderWithValidArticles
     */
    public function testValidateOrderWithValidArticles($sUserCountry, $blHasTBEArticles, $blValidArticles)
    {
        /** @var Article|EShopArticle|MockObject $oArticle */
        $oArticle = $this->createPartialMock(Article::class, ["getOeVATTBETBEVat", "isOeVATTBETBEService"]);
        $oArticle->expects($this->any())->method("isOeVATTBETBEService")->will($this->returnValue($blHasTBEArticles));
        $oArticle->expects($this->any())->method("getOeVATTBETBEVat")->will($this->returnValue($blValidArticles ? 19 : null));

        /** @var Basket|EShopBasket|MockObject $oArticle */
        $oBasket = $this->createPartialMock(Basket::class, ["getOeVATTBETbeCountryId", "hasOeTBEVATArticles", "getBasketArticles"]);
        $oBasket->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue($sUserCountry));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getBasketArticles")->will($this->returnValue([$oArticle]));
        Registry::getSession()->setVariable('TBECountryId', $sUserCountry);

        /** @var EShopUser|User $oUser */
        $oUser = oxNew(EShopUser::class);

        $oOrder = $this->createPartialMock(Order::class, ["getValidateOrderParent"]);
        $oOrder->expects($this->any())->method("getValidateOrderParent")->will($this->returnValue(0));

        $this->assertSame(0, $oOrder->validateOrder($oBasket, $oUser));
    }

    public function providerGetOeVATTBECountryTitle(): array
    {
        return [
            [0, 'Deutschland'],
            [1, 'Germany'],
        ];
    }

    /**
     * Test checks if function which is used for invoice pdf module would generate invoice with correct country.
     *
     * @param int    $iLanguageId    Which is used in invoice pdf to set language.
     * @param string $sCountryResult Country which will be displayed in invoice.
     *
     * @dataProvider providerGetOeVATTBECountryTitle
     */
    public function testGetOeVATTBECountryTitle($iLanguageId, $sCountryResult)
    {
        Registry::getLang()->setBaseLanguage($iLanguageId);

        $aEvidenceData = [
            'usedOrderEvidenceId' => ['countryId' => 'a7c40f631fc920687.20179984']
        ];
        /** @var OrderEvidenceList|MockObject $oOrderEvidenceList */
        $oOrderEvidenceList = $this->createPartialMock(OrderEvidenceList::class, ['getData', 'load']);
        $oOrderEvidenceList->expects($this->once())->method('getData')->will($this->returnValue($aEvidenceData));
        $oOrderEvidenceList->expects($this->once())->method('load');

        /** @var Order|EShopOrder|MockObject $oOrder */
        $oOrder = $this->createPartialMock(
            Order::class,
            ['factoryOeVATTBEOrderEvidenceList', 'load', 'getOeVATTBEUsedEvidenceId']
        );
        $oOrder->expects($this->any())->method('factoryOeVATTBEOrderEvidenceList')->will($this->returnValue($oOrderEvidenceList));
        $oOrder->expects($this->any())->method('getOeVATTBEUsedEvidenceId')->will($this->returnValue('usedOrderEvidenceId'));


        $this->assertSame($sCountryResult, $oOrder->getOeVATTBECountryTitle());
    }

    public function providerSetGetOrderTBEServicesInInvoice(): array
    {
        return [
            [null, false],
            [false, false],
            [true, true],
        ];
    }

    /**
     * Test for setter and getter.
     *
     * @param boolean $blValueToSet
     * @param boolean $blResult
     *
     * @dataProvider providerSetGetOrderTBEServicesInInvoice
     */
    public function testSetGetOrderTBEServicesInInvoice($blValueToSet, $blResult)
    {
        /** @var Order $oOrder */
        $oOrder = oxNew(Order::class);
        $oOrder->setOeVATTBEHasOrderTBEServicesInInvoice($blValueToSet);

        $this->assertSame($blResult, $oOrder->getOeVATTBEHasOrderTBEServicesInInvoice());
    }
}
