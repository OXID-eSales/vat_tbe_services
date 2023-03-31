<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Testing extended oxUser class.
 *
 * @covers oeVATTBEOxCmp_Basket
 */
class Unit_oeVatTbe_components_oeVATTBEOxCmpBasketTest extends OxidTestCase
{
    /**
     * Render test
     */
    public function testRenderBasketWithoutTbeCountry()
    {
        $oCountry = $this->getMock("oeVATTBEOxCountry", array("appliesOeTBEVATTbeVat"));
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(true));

        $oUser = $this->getMock('oeVATTBEOxUser', array('getOeVATTBETbeCountryId', 'hasOeTBEVATArticles'));
        $oUser->expects($this->any())->method('getOeVATTBETbeCountryId')->will($this->returnValue('DE'));

        $oBasket = $this->getMock('oeVATTBEOxBasket', array('hasOeTBEVATArticles', 'getOeVATTBECountry'));
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));
        $oBasket->expects($this->any())->method('getOeVATTBECountry')->will($this->returnValue($oCountry));

        $this->getSession()->setBasket($oBasket);

        $oCmp_Basket = oxNew('oxCmp_Basket');
        $oCmp_Basket->setUser($oUser);

        $oBasket = $oCmp_Basket->render();

        $this->assertSame('DE', $oBasket->getOeVATTBETbeCountryId());
        $this->assertTrue($oBasket->showOeVATTBECountryChangedError());
    }

    /**
     * Render test
     */
    public function testRenderBasketWithTbeCountry()
    {
        $oCountry = $this->getMock("oeVATTBEOxCountry", array("appliesOeTBEVATTbeVat"));
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(true));

        $oUser = $this->getMock('oeVATTBEOxUser', array('getOeVATTBETbeCountryId', 'hasOeTBEVATArticles'));
        $oUser->expects($this->any())->method('getOeVATTBETbeCountryId')->will($this->returnValue('DE'));

        $oBasket = $this->getMock('oeVATTBEOxBasket', array('hasOeTBEVATArticles', 'getOeVATTBECountry'));
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));
        $oBasket->expects($this->any())->method('getOeVATTBECountry')->will($this->returnValue($oCountry));
        $oBasket->setOeVATTBECountryId('LT');

        $this->getSession()->setBasket($oBasket);

        $oCmp_Basket = oxNew('oxCmp_Basket');
        $oCmp_Basket->setUser($oUser);

        $oBasket = $oCmp_Basket->render();

        $this->assertSame('DE', $oBasket->getOeVATTBETbeCountryId());
        $this->assertTrue($oBasket->showOeVATTBECountryChangedError());
    }

    /**
     * Render test
     */
    public function testRenderBasketWithNotTbeCountry()
    {
        $oCountry = $this->getMock("oeVATTBEOxCountry", array("appliesOeTBEVATTbeVat"));
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(false));

        $oUser = $this->getMock('oeVATTBEOxUser', array('getOeVATTBETbeCountryId', 'hasOeTBEVATArticles'));
        $oUser->expects($this->any())->method('getOeVATTBETbeCountryId')->will($this->returnValue('DE'));

        $oBasket = $this->getMock('oeVATTBEOxBasket', array('hasOeTBEVATArticles', 'getOeVATTBECountry'));
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));
        $oBasket->expects($this->any())->method('getOeVATTBECountry')->will($this->returnValue($oCountry));
        $oBasket->setOeVATTBECountryId('LT');

        $this->getSession()->setBasket($oBasket);

        $oCmp_Basket = oxNew('oxCmp_Basket');
        $oCmp_Basket->setUser($oUser);

        $oBasket = $oCmp_Basket->render();

        $this->assertSame('DE', $oBasket->getOeVATTBETbeCountryId());
        $this->assertFalse($oBasket->showOeVATTBECountryChangedError());
    }



    /**
     * Render test
     */
    public function testRenderBasketWithTbeCountryNoTBEArticles()
    {
        $oUser = $this->getMock('oeVATTBEOxUser', array('getOeVATTBETbeCountryId', 'hasOeTBEVATArticles'));
        $oUser->expects($this->any())->method('getOeVATTBETbeCountryId')->will($this->returnValue('DE'));

        $oBasket = $this->getMock('oeVATTBEOxBasket', array('hasOeTBEVATArticles'));
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(false));
        $oBasket->setOeVATTBECountryId('LT');

        $this->getSession()->setBasket($oBasket);

        $oCmp_Basket = oxNew('oxCmp_Basket');
        $oCmp_Basket->setUser($oUser);

        $oBasket = $oCmp_Basket->render();

        $this->assertSame('DE', $oBasket->getOeVATTBETbeCountryId());
        $this->assertFalse($oBasket->showOeVATTBECountryChangedError());
    }

    /**
     * Render test
     */
    public function testRenderBasketWithTbeSameCountry()
    {
        $oUser = $this->getMock('oeVATTBEOxUser', array('getOeVATTBETbeCountryId', 'hasOeTBEVATArticles'));
        $oUser->expects($this->any())->method('getOeVATTBETbeCountryId')->will($this->returnValue('DE'));

        $oBasket = $this->getMock('oeVATTBEOxBasket', array('hasOeTBEVATArticles'));
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));
        $oBasket->setOeVATTBECountryId('DE');

        $this->getSession()->setBasket($oBasket);

        $oCmp_Basket = oxNew('oxCmp_Basket');
        $oCmp_Basket->setUser($oUser);

        $oBasket = $oCmp_Basket->render();

        $this->assertSame('DE', $oBasket->getOeVATTBETbeCountryId());
        $this->assertFalse($oBasket->showOeVATTBECountryChangedError());
    }
}
