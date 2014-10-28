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
    public function testValidateOrderNotValidOtherValidationOk()
    {
        $oBasket = $this->getMock("oeVATTBEOxBasket", array("getTbeCountryId"));
        $oBasket->expects($this->any())->method("getTbeCountryId")->will($this->returnValue('de'));

        $oUser = $this->getMock("oeVATTBEOxUser", array("getTbeCountryId"));
        $oUser->expects($this->any())->method("getTbeCountryId")->will($this->returnValue('lt'));

        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getValidateOrderParent"));
        $oOrder->expects($this->any())->method("_getValidateOrderParent")->will($this->returnValue(0));

        $this->assertSame(oxOrder::ORDER_STATE_INVALIDDElADDRESSCHANGED, $oOrder->validateOrder($oBasket, $oUser));
    }

    public function testValidateOrderValidOtherValidationOk()
    {
        $oBasket = $this->getMock("oeVATTBEOxBasket", array("getTbeCountryId"));
        $oBasket->expects($this->any())->method("getTbeCountryId")->will($this->returnValue('de'));

        $oUser = $this->getMock("oeVATTBEOxUser", array("getTbeCountryId"));
        $oUser->expects($this->any())->method("getTbeCountryId")->will($this->returnValue('de'));

        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getValidateOrderParent"));
        $oOrder->expects($this->any())->method("_getValidateOrderParent")->will($this->returnValue(0));

        $this->assertSame(0, $oOrder->validateOrder($oBasket, $oUser));
    }

    public function testValidateOrderUserHasNoTBECountryOtherValidationOk()
    {
        $oBasket = $this->getMock("oeVATTBEOxBasket", array("getTbeCountryId"));
        $oBasket->expects($this->any())->method("getTbeCountryId")->will($this->returnValue(null));

        $oUser = $this->getMock("oeVATTBEOxUser", array("getTbeCountryId"));
        $oUser->expects($this->any())->method("getTbeCountryId")->will($this->returnValue(null));

        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getValidateOrderParent"));
        $oOrder->expects($this->any())->method("_getValidateOrderParent")->will($this->returnValue(0));

        $this->assertSame(0, $oOrder->validateOrder($oBasket, $oUser));
    }

    public function testValidateOrderValidOtherValidationNotOk()
    {
        $oBasket = $this->getMock("oeVATTBEOxBasket", array("getTbeCountryId"));
        $oBasket->expects($this->any())->method("getTbeCountryId")->will($this->returnValue('de'));

        $oUser = $this->getMock("oeVATTBEOxUser", array("getTbeCountryId"));
        $oUser->expects($this->any())->method("getTbeCountryId")->will($this->returnValue('de'));

        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getValidateOrderParent"));
        $oOrder->expects($this->any())->method("_getValidateOrderParent")->will($this->returnValue(1));

        $this->assertSame(1, $oOrder->validateOrder($oBasket, $oUser));
    }

    public function testValidateOrderArticleCheckerNotValid()
    {
        $oBasket = $this->getMock("oeVATTBEOxBasket", array("getTbeCountryId"));
        $oBasket->expects($this->any())->method("getTbeCountryId")->will($this->returnValue('de'));

        $oUser = $this->getMock("oeVATTBEOxUser", array("getTbeCountryId"));
        $oUser->expects($this->any())->method("getTbeCountryId")->will($this->returnValue('de'));

        $oChecker = $this->getMock("oeVATTBEOrderArticleChecker", array("isValid"), array(), '', false);
        $oChecker->expects($this->any())->method("isValid")->will($this->returnValue(false));

        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getValidateOrderParent", "_getOeVATTBEOrderArticleChecker"));
        $oOrder->expects($this->any())->method("_getValidateOrderParent")->will($this->returnValue(0));
        $oOrder->expects($this->any())->method("_getOeVATTBEOrderArticleChecker")->will($this->returnValue($oChecker));

        $this->assertSame(oeVATTBEOxOrder::ORDER_STATE_TBE_NOT_CONFIGURED, $oOrder->validateOrder($oBasket, $oUser));
    }

    public function testValidateOrderArticleCheckerValid()
    {
        $oBasket = $this->getMock("oeVATTBEOxBasket", array("getTbeCountryId"));
        $oBasket->expects($this->any())->method("getTbeCountryId")->will($this->returnValue('de'));

        $oUser = $this->getMock("oeVATTBEOxUser", array("getTbeCountryId"));
        $oUser->expects($this->any())->method("getTbeCountryId")->will($this->returnValue('de'));

        $oChecker = $this->getMock("oeVATTBEOrderArticleChecker", array("isValid"), array(), '', false);
        $oChecker->expects($this->any())->method("isValid")->will($this->returnValue(true));

        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getValidateOrderParent", "_getOeVATTBEOrderArticleChecker"));
        $oOrder->expects($this->any())->method("_getValidateOrderParent")->will($this->returnValue(0));
        $oOrder->expects($this->any())->method("_getOeVATTBEOrderArticleChecker")->will($this->returnValue($oChecker));

        $this->assertSame(0, $oOrder->validateOrder($oBasket, $oUser));
    }

    public function testValidateOrderArticleCheckerInValidParentInvalid()
    {
        $oBasket = $this->getMock("oeVATTBEOxBasket", array("getTbeCountryId"));
        $oBasket->expects($this->any())->method("getTbeCountryId")->will($this->returnValue('de'));

        $oUser = $this->getMock("oeVATTBEOxUser", array("getTbeCountryId"));
        $oUser->expects($this->any())->method("getTbeCountryId")->will($this->returnValue('de'));

        $oChecker = $this->getMock("oeVATTBEOrderArticleChecker", array("isValid"), array(), '', false);
        $oChecker->expects($this->any())->method("isValid")->will($this->returnValue(false));


        $oOrder = $this->getMock("oeVATTBEOxOrder", array("_getValidateOrderParent", "_getOeVATTBEOrderArticleChecker"));
        $oOrder->expects($this->any())->method("_getValidateOrderParent")->will($this->returnValue(1));
        $oOrder->expects($this->any())->method("_getOeVATTBEOrderArticleChecker")->will($this->returnValue($oChecker));

        $this->assertSame(1, $oOrder->validateOrder($oBasket, $oUser));
    }

    public function providerOeVATTBEGetCountryTitle()
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
     * @dataProvider providerOeVATTBEGetCountryTitle
     */
    public function testOeVATTBEGetCountryTitle($iLanguageId, $sCountryResult)
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
            array('getSelectedLang', '_factoryOeVATTBEOrderEvidenceList', 'load', '_oeVATTBEGetUsedEvidenceId')
        );
        $oOrder->expects($this->any())->method('getSelectedLang')->will($this->returnValue($iLanguageId));
        $oOrder->expects($this->any())->method('_factoryOeVATTBEOrderEvidenceList')->will($this->returnValue($oOrderEvidenceList));
        $oOrder->expects($this->any())->method('_oeVATTBEGetUsedEvidenceId')->will($this->returnValue('usedOrderEvidenceId'));


        $this->assertSame($sCountryResult, $oOrder->oeVATTBEGetCountryTitle());
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
        $oOrder = oxNew('oeVATTBEOxOrder');
        $oOrder->setHasOrderTBEServicesInInvoice($blValueToSet);

        $this->assertSame($blResult, $oOrder->getHasOrderTBEServicesInInvoice());
    }
}
