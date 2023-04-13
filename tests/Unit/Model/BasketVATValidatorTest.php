<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model;

use OxidEsales\Eshop\Application\Model\BasketItem as EShopBasketItem;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\BasketVATValidator;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\Eshop\Application\Model\Article as EShopArticle;
use OxidEsales\EVatModule\Shop\Basket;
use OxidEsales\Eshop\Application\Model\Basket as EShopBasket;
use OxidEsales\EVatModule\Shop\Country;
use OxidEsales\Eshop\Application\Model\Country as EShopCountry;
use OxidEsales\EVatModule\Model\User as UserModel;
use OxidEsales\EVatModule\Shop\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Testing BasketVATValidator class.
 *
 * @covers BasketVATValidator
 */
class BasketVATValidatorTest extends TestCase
{
    /**
     * data provider for test testShowVATTBEMark
     *
     * @return array
     */
    public function providerShowVATTBEMark(): array
    {
        return [
            [true, true, true, true],
            [false, true, true, true],
            [false, true, false, true],
            [true, false, true, false],
            [false, false, true, false],
            [true, true, false, false],
            [true, false, false, false],
            [false, false, false, false],
        ];
    }

    /**
     * Basket Vat Validator test for showVATTBEMark method.
     *
     * @param bool $blIsUserLoggedIn      User logged in or not
     * @param bool $blIsArticleTbeService Article tbe or not
     * @param bool $blIsCountryConfigured Configured country or not
     * @param bool $blResult              Expected result
     *
     * @dataProvider providerShowVATTBEMark
     */
    public function testShowVATTBEMark($blIsUserLoggedIn, $blIsArticleTbeService, $blIsCountryConfigured, $blResult)
    {
        /** @var Country|EShopCountry|MockObject $oBasketItem */
        $oCountry = $this->createPartialMock(Country::class, ['appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue($blIsCountryConfigured));

        /** @var Basket|EShopBasket|MockObject $oBasketItem */
        $oBasket = $this->createPartialMock(Basket::class, ['getOeVATTBECountry', 'isOeVATTBEValid']);
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue(true));

        /** @var Country|MockObject $oBasketItem */
        $oTBEUserCountry = $this->createPartialMock(UserModel::class, ['isUserFromDomesticCountry']);
        $oTBEUserCountry->expects($this->any())->method("isUserFromDomesticCountry")->will($this->returnValue(false));

        /** @var User|null $oUser */
        $oUser = ($blIsUserLoggedIn) ? oxNew(User::class) : null;
        Registry::getSession()->setUser($oUser);

        /** @var Article|EShopArticle|MockObject $oBasketItem */
        $oArticle = $this->createPartialMock(Article::class, ['isOeVATTBETBEService']);
        $oArticle->expects($this->any())->method("isOeVATTBETBEService")->will($this->returnValue($blIsArticleTbeService));

        /** @var EShopBasketItem|MockObject $oBasketItem */
        $oBasketItem = $this->createPartialMock(EShopBasketItem::class, ['getVatPercent', 'getArticle']);
        $oBasketItem->expects($this->any())->method("getVatPercent")->will($this->returnValue(10));
        $oBasketItem->expects($this->any())->method("getArticle")->will($this->returnValue($oArticle));

        /** @var BasketVATValidator $oValidator */
        $oValidator = oxNew(BasketVATValidator::class, Registry::getSession(), $oTBEUserCountry);

        $this->assertSame($blResult, $oValidator->showVATTBEMark($oBasketItem));
    }

    /**
     * data provider for test testIsTBEArticleValid
     *
     * @return array
     */
    public function providerIsTBEArticleValid(): array
    {
        return [
            [false, false],
            [true, true],
        ];
    }

    /**
     * Basket Vat Validator test for isTBEArticleValid method.
     *
     * @param bool   $blIsArticleValid Article is valid / invalid
     * @param string $sExpectValue     Expected value
     *
     * @dataProvider providerIsTBEArticleValid
     */
    public function testIsTBEArticleValid($blIsArticleValid, $sExpectValue)
    {
        $oCountry = $this->createPartialMock(Country::class, ['appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(true));

        $oArticle = $this->createPartialMock(Article::class, ['isOeVATTBETBEService', 'getId']);
        $oArticle->expects($this->any())->method("isOeVATTBETBEService")->will($this->returnValue(true));
        $ArticleId = ($blIsArticleValid) ? 'id' : 'id1';
        $oArticle->expects($this->any())->method("getId")->will($this->returnValue($ArticleId));

        $oBasketItem = $this->createPartialMock(EShopBasketItem::class, ['getVatPercent', 'getArticle']);
        $oBasketItem->expects($this->any())->method("getVatPercent")->will($this->returnValue(10));
        $oBasketItem->expects($this->any())->method("getArticle")->will($this->returnValue($oArticle));

        $oBasket = $this->createPartialMock(Basket::class, ['getOeVATTBECountry', 'isOeVATTBEValid', 'getOeVATTBEInValidArticles']);
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue(false));
        $aInValidArticles = ['id1' => 'article1', 'id2' => 'article2'];
        $oBasket->expects($this->any())->method("getOeVATTBEInValidArticles")->will($this->returnValue($aInValidArticles));

        /** @var UserModel|MockObject $oBasketItem */
        $oTBEUserCountry = $this->createPartialMock(UserModel::class, ['isUserFromDomesticCountry']);
        $oTBEUserCountry->expects($this->any())->method("isUserFromDomesticCountry")->will($this->returnValue(false));

        /** @var BasketVATValidator $oValidator */
        $oValidator = oxNew(BasketVATValidator::class, Registry::getSession(), $oTBEUserCountry);

        $this->assertSame($sExpectValue, $oValidator->isArticleValid($oBasketItem));
    }

    /**
     * When user is from domestic country, all articles should always be valid.
     */
    public function testIsTBEArticleValidWhenUserFromDomesticCountry()
    {
        $oCountry = $this->createPartialMock(Country::class, ['appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(true));

        $oArticle = $this->createPartialMock(Article::class, ['isOeVATTBETBEService', 'getId']);
        $oArticle->expects($this->any())->method("isOeVATTBETBEService")->will($this->returnValue(true));
        $oArticle->expects($this->any())->method("getId")->will($this->returnValue('invalid_article_id'));

        $oBasketItem = $this->createPartialMock(EShopBasketItem::class, ['getArticle']);
        $oBasketItem->expects($this->any())->method("getArticle")->will($this->returnValue($oArticle));

        $aInValidArticles = ['invalid_article_id' => 'article1'];
        $oBasket = $this->createPartialMock(Basket::class, ['getOeVATTBECountry', 'isOeVATTBEValid', 'getOeVATTBEInValidArticles']);
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue(false));
        $oBasket->expects($this->any())->method("getOeVATTBEInValidArticles")->will($this->returnValue($aInValidArticles));

        /** @var UserModel|MockObject $oBasketItem */
        $oTBEUserCountry = $this->createPartialMock(UserModel::class, ['isUserFromDomesticCountry']);
        $oTBEUserCountry->expects($this->any())->method("isUserFromDomesticCountry")->will($this->returnValue(true));

        /** @var BasketVATValidator $oValidator */
        $oValidator = oxNew(BasketVATValidator::class, Registry::getSession(), $oTBEUserCountry);

        $this->assertTrue($oValidator->isArticleValid($oBasketItem));
    }

    /**
     * When user is from domestic country, no marks should be added to any articles.
     */
    public function testShowVATTBEMarkWhenUserFromDomesticCountry()
    {
        /** @var Country|EShopCountry|MockObject $oBasketItem */
        $oCountry = $this->createPartialMock(Country::class, ['appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method("appliesOeTBEVATTbeVat")->will($this->returnValue(true));

        /** @var Article|EShopArticle|MockObject $oBasketItem */
        $oArticle = $this->createPartialMock(Article::class, ['isOeVATTBETBEService']);
        $oArticle->expects($this->any())->method("isOeVATTBETBEService")->will($this->returnValue(true));

        /** @var EShopBasketItem|MockObject $oBasketItem */
        $oBasketItem = $this->createPartialMock(EShopBasketItem::class, ['getArticle']);
        $oBasketItem->expects($this->any())->method("getArticle")->will($this->returnValue($oArticle));

        /** @var Basket|EShopBasket|MockObject $oBasketItem */
        $oBasket = $this->createPartialMock(Basket::class, ['getOeVATTBECountry', 'isOeVATTBEValid']);
        $oBasket->expects($this->any())->method("getOeVATTBECountry")->will($this->returnValue($oCountry));
        $oBasket->expects($this->any())->method("isOeVATTBEValid")->will($this->returnValue(true));

        /** @var UserModel|MockObject $oBasketItem */
        $oTBEUserCountry = $this->createPartialMock(UserModel::class, ['isUserFromDomesticCountry']);
        $oTBEUserCountry->expects($this->any())->method("isUserFromDomesticCountry")->will($this->returnValue(true));

        /** @var BasketVATValidator $oValidator */
        $oValidator = oxNew(BasketVATValidator::class, Registry::getSession(), $oTBEUserCountry);

        $this->assertFalse($oValidator->showVATTBEMark($oBasketItem));
    }
}
