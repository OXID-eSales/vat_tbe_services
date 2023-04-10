<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Shop;

use OxidEsales\Eshop\Core\Registry;
use PHPUnit\Framework\TestCase;

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
        $oBasket = $this->getMock("oeVATTBEOxBasket", array("getOeVATTBETbeCountryId"));
        $oBasket->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue('LithuaniaId'));
        Registry::getSession()->setVariable('TBECountryId', 'GermanyId');

        /** @var oxUser|oeVATTBEOxUser $oUser */
        $oUser = oxNew('oxUser');

        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getValidateOrderParent"));
        $oOrder->expects($this->any())->method("_getValidateOrderParent")->will($this->returnValue(0));

        $this->assertSame(oxOrder::ORDER_STATE_INVALIDDElADDRESSCHANGED, $oOrder->validateOrder($oBasket, $oUser));
    }

    /**
     * When user and basket countries does not match but order already had errors, error code should not be changed.
     */
    public function testValidateOrderWhenUserDoesNotMatchBasketAddressAndOrderHadError()
    {
        $oBasket = $this->getMock("oeVATTBEOxBasket", array("getOeVATTBETbeCountryId"));
        $oBasket->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue('LithuaniaId'));
        Registry::getSession()->setVariable('TBECountryId', 'GermanyId');

        /** @var oxUser|oeVATTBEOxUser $oUser */
        $oUser = oxNew('oxUser');

        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getValidateOrderParent"));
        $oOrder->expects($this->any())->method("_getValidateOrderParent")->will($this->returnValue(oxOrder::ORDER_STATE_PAYMENTERROR));

        $this->assertSame(oxOrder::ORDER_STATE_PAYMENTERROR, $oOrder->validateOrder($oBasket, $oUser));
    }

    /**
     * provider for testValidateOrderWithInvalidArticles
     *
     * @return array
     */
    public function providerValidateOrderWithInvalidArticles()
    {
        // Non domestic country.
        $sAustriaId = 'a7c40f6320aeb2ec2.72885259';

        return array(
            array($sAustriaId, false),
            array('NonExistingCountry', true),
            array('', true),
        );
    }

    /**
     * Test order validation when order should be invalid because of invalid articles.
     *
     * @param string $sUserCountry    User country.
     * @param bool   $blValidArticles Whether basket articles are valid.
     *
     * @dataProvider providerValidateOrderWithInvalidArticles
     */
    public function testValidateOrderWithInvalidArticles($sUserCountry, $blValidArticles)
    {
        /** @var oxArticle|oeVATTBEOxArticle|PHPUnit_Framework_MockObject_MockObject $oArticle */
        $oArticle = $this->getMock("oeVATTBEOxArticle", array("getOeVATTBETBEVat", "isOeVATTBETBEService"));
        $oArticle->expects($this->any())->method("isOeVATTBETBEService")->will($this->returnValue(true));
        $oArticle->expects($this->any())->method("getOeVATTBETBEVat")->will($this->returnValue($blValidArticles ? 19 : null));

        /** @var oxBasket|oeVATTBEOxBasket|PHPUnit_Framework_MockObject_MockObject $oArticle */
        $oBasket = $this->getMock("oeVATTBEOxBasket", array("getOeVATTBETbeCountryId", "hasOeTBEVATArticles", "getBasketArticles"));
        $oBasket->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue($sUserCountry));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getBasketArticles")->will($this->returnValue(array($oArticle)));
        Registry::getSession()->setVariable('TBECountryId', $sUserCountry);

        /** @var oxUser|oeVATTBEOxUser $oUser */
        $oUser = oxNew('oxUser');

        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getValidateOrderParent"));
        $oOrder->expects($this->any())->method("_getValidateOrderParent")->will($this->returnValue(0));

        $this->assertSame(oeVATTBEOxOrder::ORDER_STATE_TBE_NOT_CONFIGURED, $oOrder->validateOrder($oBasket, $oUser));
    }

    /**
     * provider for testValidateOrderWithValidArticles
     *
     * @return array
     */
    public function providerValidateOrderWithValidArticles()
    {
        // Domestic country.
        $sGermanyId = 'a7c40f631fc920687.20179984';

        // Non domestic non EU country.
        $sAustraliaId = '8f241f11095410f38.37165361';

        return array(
            array($sGermanyId, true, true),
            array($sGermanyId, false, true),
            array($sGermanyId, false, false),
            array($sAustraliaId, true, true),
            array($sAustraliaId, false, true),
            array($sAustraliaId, false, false),
        );
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
        /** @var oxArticle|oeVATTBEOxArticle|PHPUnit_Framework_MockObject_MockObject $oArticle */
        $oArticle = $this->getMock("oeVATTBEOxArticle", array("getOeVATTBETBEVat", "isOeVATTBETBEService"));
        $oArticle->expects($this->any())->method("isOeVATTBETBEService")->will($this->returnValue($blHasTBEArticles));
        $oArticle->expects($this->any())->method("getOeVATTBETBEVat")->will($this->returnValue($blValidArticles ? 19 : null));

        /** @var oxBasket|oeVATTBEOxBasket|PHPUnit_Framework_MockObject_MockObject $oArticle */
        $oBasket = $this->getMock("oeVATTBEOxBasket", array("getOeVATTBETbeCountryId", "hasOeTBEVATArticles", "getBasketArticles"));
        $oBasket->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue($sUserCountry));
        $oBasket->expects($this->any())->method("hasOeTBEVATArticles")->will($this->returnValue(true));
        $oBasket->expects($this->any())->method("getBasketArticles")->will($this->returnValue(array($oArticle)));
        Registry::getSession()->setVariable('TBECountryId', $sUserCountry);

        /** @var oxUser|oeVATTBEOxUser $oUser */
        $oUser = oxNew('oxUser');

        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getValidateOrderParent"));
        $oOrder->expects($this->any())->method("_getValidateOrderParent")->will($this->returnValue(0));

        $this->assertSame(0, $oOrder->validateOrder($oBasket, $oUser));
    }

    public function providerGetOeVATTBECountryTitle()
    {
        return array(
            array(0, 'Deutschland'),
            array(1, 'Germany'),
        );
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
        $aEvidenceData = array(
            'usedOrderEvidenceId' => array('countryId' => 'a7c40f631fc920687.20179984')
        );
        /** @var oeVATTBEOrderEvidenceList|PHPUnit_Framework_MockObject_MockObject $oOrderEvidenceList */
        $oOrderEvidenceList = $this->getMock('oeVATTBEOrderEvidenceList', array('getData', 'load'), array(), '', false);
        $oOrderEvidenceList->expects($this->once())->method('getData')->will($this->returnValue($aEvidenceData));
        $oOrderEvidenceList->expects($this->once())->method('load');

        /** @var oxOrder|oeVATTBEOxOrder|PHPUnit_Framework_MockObject_MockObject $oOrder */
        $oOrder = $this->getMock(
            'oeVATTBEOxOrder',
            array('getSelectedLang', '_factoryOeVATTBEOrderEvidenceList', 'load', '_getOeVATTBEUsedEvidenceId')
        );
        $oOrder->expects($this->any())->method('getSelectedLang')->will($this->returnValue($iLanguageId));
        $oOrder->expects($this->any())->method('_factoryOeVATTBEOrderEvidenceList')->will($this->returnValue($oOrderEvidenceList));
        $oOrder->expects($this->any())->method('_getOeVATTBEUsedEvidenceId')->will($this->returnValue('usedOrderEvidenceId'));


        $this->assertSame($sCountryResult, $oOrder->getOeVATTBECountryTitle());
    }

    public function providerSetGetOrderTBEServicesInInvoice()
    {
        return array(
            array(null, false),
            array(false, false),
            array(true, true),
        );
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
        /** @var oeVATTBEOxOrder $oOrder */
        $oOrder = oxNew('oxOrder');
        $oOrder->setOeVATTBEHasOrderTBEServicesInInvoice($blValueToSet);

        $this->assertSame($blResult, $oOrder->getOeVATTBEHasOrderTBEServicesInInvoice());
    }
}
