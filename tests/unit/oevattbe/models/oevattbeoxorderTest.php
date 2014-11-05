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
 * Testing extended oxArticle class.
 *
 * @covers oeVATTBEOxOrder
 */
class Unit_oeVATTBE_models_oeVATTBEOxOrderTest extends OxidTestCase
{

    /**
     * When user and basket countries does not match error code should be returned.
     */
    public function testValidateOrderWhenUserDoesNotMatchBasketAddress()
    {
        $oBasket = $this->getMock("oeVATTBEOxBasket", array("getOeVATTBETbeCountryId"));
        $oBasket->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue('LithuaniaId'));
        $this->getSession()->setVariable('TBECountryId', 'GermanyId');

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
        $this->getSession()->setVariable('TBECountryId', 'GermanyId');

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
        return array(
            array('a7c40f631fc920687.20179984', false),
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
        $this->getSession()->setVariable('TBECountryId', $sUserCountry);

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
        return array(
            array('a7c40f631fc920687.20179984', true, true),  // DE
            array('a7c40f631fc920687.20179984', false, false), // AU
            array('8f241f11095410f38.37165361', true, false), // AU
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
        $this->getSession()->setVariable('TBECountryId', $sUserCountry);

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
