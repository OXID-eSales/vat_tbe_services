<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Model;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EVatModule\Model\OrderArticleChecker;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\EVatModule\Shop\Country;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use OxidEsales\EVatModule\Model\User as UserModel;

/**
 * Testing OrderArticleChecker class.
 */
class OrderArticleCheckerTest extends TestCase
{
    use ContainerTrait;

    public function tearDown(): void
    {
        Registry::set(Session::class, oxNew(Session::class));
    }

    /**
     * Provider for test.
     *
     * @return array
     */
    public static function providerCheckingArticlesWithEmptyList()
    {
        return [
            [[]],
            [''],
            [null],
            [false],
        ];
    }

    /**
     * Checks articles with empty list.
     *
     * @dataProvider providerCheckingArticlesWithEmptyList
     */
    public function testCheckingArticlesWithEmptyList($articles)
    {
        $this->mockSessionBasket($articles);

        $oCountry = $this->createPartialMock(Country::class, ['isInEU', 'appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->createPartialMock(UserModel::class, ['getCountry']);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = oxNew(OrderArticleChecker::class, $oUser);

        $this->assertSame(true, $oChecker->isValid());
    }

    /**
     * Checks articles if valid.
     */
    public function testCheckingArticlesWhenCorrectArticlesExists()
    {
        $oArticleWithoutVAT = $this->createArticle(false, null);
        $oArticleWithVAT = $this->createArticle(false, 15);
        $oTBEArticleWithVAT = $this->createArticle(true, 15);
        $oTBEArticleWithZeroVAT = $this->createArticle(true, 0);
        $this->mockSessionBasket([$oArticleWithoutVAT, $oArticleWithVAT, $oTBEArticleWithVAT, $oTBEArticleWithZeroVAT]);

        $oCountry = $this->createPartialMock(Country::class, ['isInEU', 'appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->createPartialMock(UserModel::class, ['getCountry']);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = oxNew(OrderArticleChecker::class, $oUser);

        $this->assertTrue($oChecker->isValid());
    }

    /**
     * Checks articles if not valid.
     */
    public function testCheckingArticlesWhenCorrectArticlesExistsButCountryNot()
    {
        $oArticleWithoutVAT = $this->createArticle(false, null);
        $oArticleWithVAT = $this->createArticle(false, 15);
        $oTBEArticleWithVAT = $this->createArticle(true, 15);
        $oTBEArticleWithZeroVAT = $this->createArticle(true, 0);
        $this->mockSessionBasket([$oArticleWithoutVAT, $oArticleWithVAT, $oTBEArticleWithVAT, $oTBEArticleWithZeroVAT]);

        $oUser = $this->createPartialMock(UserModel::class, ['getCountry']);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue(null));

        $oChecker = oxNew(OrderArticleChecker::class, $oUser);

        $this->assertFalse($oChecker->isValid());
    }

    /**
     * Checks articles if not valid.
     *
     * @return OrderArticleChecker
     */
    public function testCheckingArticlesWhenIncorrectArticlesExists()
    {
        $oArticleWithoutVAT = $this->createArticle(false, null);
        $oTBEArticleWithoutVAT = $this->createArticle(true, null);
        $this->mockSessionBasket([$oArticleWithoutVAT, $oTBEArticleWithoutVAT]);

        $oCountry = $this->createPartialMock(Country::class, ['isInEU', 'appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->createPartialMock(UserModel::class, ['getCountry']);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = oxNew(OrderArticleChecker::class, $oUser);

        $this->assertFalse($oChecker->isValid());
    }

    /**
     * Checks invalid articles.
     */
    public function testReturningInvalidArticlesWhenIncorrectArticlesExists()
    {
        $oArticleWithoutVAT = $this->createArticle(false, null, 'id');
        $oTBEArticleWithoutVAT1 = $this->createArticle(true, null, 'id1');
        $oTBEArticleWithoutVAT2 = $this->createArticle(true, null, 'id2');
        $this->mockSessionBasket([$oArticleWithoutVAT, $oTBEArticleWithoutVAT1, $oTBEArticleWithoutVAT2]);

        $oCountry = $this->createPartialMock(Country::class, ['isInEU', 'appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->createPartialMock(UserModel::class, ['getCountry']);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = oxNew(OrderArticleChecker::class, $oUser);

        $aIncorrectArticles = ['id1' => $oTBEArticleWithoutVAT1, 'id2' => $oTBEArticleWithoutVAT2];

        $this->assertSame($aIncorrectArticles, $oChecker->getInvalidArticles());
    }

    /**
     * Checks articles if valid.
     */
    public function testCheckingArticlesWhenIncorrectArticlesExistsButCountryIsNotEu()
    {
        $oArticleWithoutVAT = $this->createArticle(false, null);
        $oTBEArticleWithoutVAT = $this->createArticle(true, null);
        $this->mockSessionBasket([$oArticleWithoutVAT, $oTBEArticleWithoutVAT]);

        $oCountry = $this->createPartialMock(Country::class, ['isInEU', 'appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(false));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->createPartialMock(UserModel::class, ['getCountry']);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = oxNew(OrderArticleChecker::class, $oUser);

        $this->assertTrue($oChecker->isValid());
    }

    /**
     * Checks articles if valid.
     */
    public function testCheckingArticlesWhenIncorrectArticlesExistsButCountryIsEuButNotTBE()
    {
        $oArticleWithoutVAT = $this->createArticle(false, null);
        $oTBEArticleWithoutVAT = $this->createArticle(true, null);
        $this->mockSessionBasket([$oArticleWithoutVAT, $oTBEArticleWithoutVAT]);

        $oCountry = $this->createPartialMock(Country::class, ['isInEU', 'appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(false));

        $oUser = $this->createPartialMock(UserModel::class, ['getCountry']);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = oxNew(OrderArticleChecker::class, $oUser);

        $this->assertTrue($oChecker->isValid());
    }

    /**
     * Creates article.
     *
     * @param bool   $blTBEService is article tbe service or not
     * @param int    $iVat         VAT rate
     * @param string $sId          article id
     *
     * @return Article|MockObject
     */
    protected function createArticle($blTBEService, $iVat, $sId = null)
    {
        $oArticle = $this->createPartialMock(Article::class, ['isOeVATTBETBEService', 'getOeVATTBETBEVat', 'getId']);
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->will($this->returnValue($blTBEService));
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->will($this->returnValue($iVat));
        if (!is_null($sId)) {
            $oArticle->expects($this->any())->method('getId')->will($this->returnValue($sId));
        }

        return $oArticle;
    }

    protected function mockSessionBasket(mixed $articles)
    {
        $basketMock = $this->createPartialMock(Basket::class, ['getBasketArticles']);
        $basketMock->method('getBasketArticles')->willReturn($articles);

        $sessionBasketMock = $this->createPartialMock(Session::class, ['getBasket']);
        $sessionBasketMock->method('getBasket')->willReturn($basketMock);

        Registry::set(Session::class, $sessionBasketMock);
    }
}
