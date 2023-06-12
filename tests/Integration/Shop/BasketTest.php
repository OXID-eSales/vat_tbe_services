<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Shop;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\OrderArticleChecker;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Shop\Basket;
use OxidEsales\Eshop\Application\Model\Basket as EShopBasket;
use OxidEsales\EVatModule\Traits\ServiceContainer;
use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxArticle class.
 */
class BasketTest extends TestCase
{
    use ServiceContainer;

    /**
     * Test for tbe country id setter and getter
     */
    public function testSetgetOeVATTBETbeCountryId()
    {
        $oBasket = oxNew(Basket::class);
        $oBasket->setOeVATTBECountryId('de');
        $this->assertSame('de', $oBasket->getOeVATTBETbeCountryId());
    }

    /**
     * Test get country when it is not set
     */
    public function testGetOeVATTBETbeCountryIdNotSet()
    {
        $oBasket = oxNew(Basket::class);
        $this->assertNull($oBasket->getOeVATTBECountry());
    }

    /**
     * Data provider for testSetCountryIdOnChangeEvent test.
     *
     * @return array
     */
    public function providerSetCountryIdOnChangeEvent(): array
    {
        return [
            [true, true, true],
            [false, false, true],
            [false, false, false],
        ];
    }

    /**
     * Test on basket country change event when no message should be shown after country change.
     *
     * @param bool $blDomesticCountry     Is user from shops domestic country.
     * @param bool $blTBECountry          Is user country TBE country.
     * @param bool $blIsArticleTbeService Is basket article TBE service.
     *
     * @dataProvider providerSetCountryIdOnChangeEvent
     */
    public function testSetCountryIdOnChangeEvent($blDomesticCountry, $blTBECountry, $blIsArticleTbeService)
    {
        $sDomesticCountry = $blDomesticCountry ? 'LT' : 'DE';
        $sLithuaniaId = '8f241f11095d6ffa8.86593236';
        Registry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', $sDomesticCountry);
        Registry::getSession()->setVariable('TBECountryId', $sLithuaniaId);

        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveDomesticCountry($sDomesticCountry);

        /** @var Country $oCountry */
        $oCountry = oxNew(Country::class);
        $oCountry->load($sLithuaniaId);
        $oCountry->oxcountry__oevattbe_appliestbevat = new Field($blTBECountry);
        $oCountry->save();

        /** @var Article $oArticle */
        $oArticle = oxNew(Article::class);
        $oArticle->setId('_testArticle1');
        $oArticle->oxarticles__oevattbe_istbeservice = new Field($blIsArticleTbeService);
        $oArticle->save();

        /** @var Basket|EShopBasket $oBasket */
        $oBasket = oxNew(EShopBasket::class);
        $oBasket->setUser(oxNew(User::class));
        $oBasket->addToBasket('_testArticle1', 1);
        $oBasket->setOeVATTBECountryId($sLithuaniaId);

        $this->assertFalse($oBasket->showOeVATTBECountryChangedError());
    }

    /**
     * Provides information if need to add article to basket or leave it empty.
     *
     * @return array
     */
    public function providerSetCountryIdOnChangeEventWhenMessageShouldBeShown(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * Test on basket country change event when message should be shown after country change.
     *
     * @param bool $bAddToBasket if some article are in basket.
     *
     * @dataProvider providerSetCountryIdOnChangeEventWhenMessageShouldBeShown
     */
    public function testSetCountryIdOnChangeEventWhenMessageShouldBeShown($bAddToBasket)
    {
        Registry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');

        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveDomesticCountry('DE');

        $sLithuaniaId = '8f241f11095d6ffa8.86593236';
        Registry::getSession()->setVariable('TBECountryId', $sLithuaniaId); // LT

        /** @var Country $oCountry */
        $oCountry = oxNew(Country::class);
        $oCountry->load($sLithuaniaId);
        $oCountry->oxcountry__oevattbe_appliestbevat = new Field(true);
        $oCountry->save();

        /** @var Article $oArticle */
        $oArticle = oxNew(Article::class);
        $oArticle->setId('_testArticle1');
        $oArticle->oxarticles__oevattbe_istbeservice = new Field(true);
        $oArticle->save();

        /** @var EShopBasket|Basket $oBasket */
        $oBasket = oxNew(EShopBasket::class);
        $oBasket->setUser(oxNew(User::class));
        $oBasket->setOeVATTBECountryId($sLithuaniaId);
        if ($bAddToBasket) {
            $oBasket->addToBasket('_testArticle1', 1);
        }

        $this->assertTrue($oBasket->showOeVATTBECountryChangedError());
    }

    /**
     * Test get country when it is set
     */
    public function testGetOeVATTBETbeCountryIdSet()
    {
        $oBasket = oxNew(Basket::class);
        $oBasket->setOeVATTBECountryId('a7c40f631fc920687.20179984');
        $this->assertSame('Deutschland', $oBasket->getOeVATTBECountry()->getFieldData('oxtitle'));
    }

    /**
     * Show error default value
     */
    public function testShowOeVATTBECountryChangedErrorDefault()
    {
        $oBasket = oxNew(Basket::class);
        $this->assertFalse($oBasket->showOeVATTBECountryChangedError());
    }

    /**
     * Show error after set and show one time
     */
    public function testShowOeVATTBECountryChangedErrorShow()
    {
        $oBasket = oxNew(Basket::class);
        $oBasket->setOeVATTBECountryChanged();
        $this->assertTrue($oBasket->showOeVATTBECountryChangedError());
        $this->assertFalse($oBasket->showOeVATTBECountryChangedError());
    }

    /**
     * test for basket validation
     */
    public function testisOeVATTBEValidValid()
    {
        $oChecker = $this->createPartialMock(OrderArticleChecker::class, ['isValid']);
        $oChecker->expects($this->any())->method('isValid')->will($this->returnValue(true));

        $oBasket = $this->createPartialMock(Basket::class, ['getOeVATTBEOrderArticleChecker']);
        $oBasket->expects($this->any())->method('getOeVATTBEOrderArticleChecker')->will($this->returnValue($oChecker));

        $this->assertTrue($oBasket->isOeVATTBEValid());
    }

    /**
     * test for basket validation
     */
    public function testisOeVATTBEValidNotValid()
    {
        $oChecker = $this->createPartialMock(OrderArticleChecker::class, ['isValid']);
        $oChecker->expects($this->any())->method('isValid')->will($this->returnValue(false));

        $oBasket = $this->createPartialMock(Basket::class, ['getOeVATTBEOrderArticleChecker']);
        $oBasket->expects($this->any())->method('getOeVATTBEOrderArticleChecker')->will($this->returnValue($oChecker));

        $this->assertFalse($oBasket->isOeVATTBEValid());
    }
}
