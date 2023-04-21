<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EVatModule\Model\OrderArticleChecker;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\EVatModule\Shop\Country;
use OxidEsales\EVatModule\Shop\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use OxidEsales\EVatModule\Model\User as UserModel;

/**
 * Testing OrderArticleChecker class.
 */
class OrderArticleCheckerTest extends TestCase
{
    use ContainerTrait;

    /**
     * Checks articles with empty list.
     *
     * @param array|null $mEmptyList article list
     * @param User       $oUser      user
     */
    public function testCheckingArticlesWithEmptyList()
    {
        $oChecker = $this->get(OrderArticleChecker::class);

        $this->assertSame(true, $oChecker->isValid());
    }

    /**
     * Checks articles if valid.
     */
    public function testCheckingArticlesWhenCorrectArticlesExists()
    {
        $oCountry = $this->createPartialMock(Country::class, ['isInEU', 'appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->createPartialMock(User::class, ['getCountry']);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = $this->get(OrderArticleChecker::class);

        $this->assertTrue($oChecker->isValid());
    }

    /**
     * Checks articles if not valid.
     */
    public function testCheckingArticlesWhenCorrectArticlesExistsButCountryNot()
    {
        $oUser = $this->createPartialMock(UserModel::class, ['getCountry']);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue(null));

        $oChecker = $this->get(OrderArticleChecker::class);

        $this->assertFalse($oChecker->isValid());
    }

    /**
     * Checks articles if not valid.
     *
     * @return OrderArticleChecker
     */
    public function testCheckingArticlesWhenIncorrectArticlesExists()
    {
        $oCountry = $this->createPartialMock(Country::class, ['isInEU', 'appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->createPartialMock(UserModel::class, ['getCountry']);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = $this->get(OrderArticleChecker::class);

        $this->assertFalse($oChecker->isValid());

        return $oChecker;
    }

    /**
     * Checks invalid articles.
     */
    public function testReturningInvalidArticlesWhenIncorrectArticlesExists()
    {
        $oTBEArticleWithoutVAT1 = $this->_createArticle(true, null, 'id1');
        $oTBEArticleWithoutVAT2 = $this->_createArticle(true, null, 'id2');

        $oCountry = $this->createPartialMock(Country::class, ['isInEU', 'appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->createPartialMock(UserModel::class, ['getCountry']);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = $this->get(OrderArticleChecker::class);

        $aIncorrectArticles = ['id1' => $oTBEArticleWithoutVAT1, 'id2' => $oTBEArticleWithoutVAT2];

        $this->assertSame($aIncorrectArticles, $oChecker->getInvalidArticles());
    }

    /**
     * Checks articles if valid.
     *
     * @return OrderArticleChecker
     */
    public function testCheckingArticlesWhenIncorrectArticlesExistsButCountryIsNotEu()
    {
        $oCountry = $this->createPartialMock(Country::class, ['isInEU', 'appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(false));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->createPartialMock(User::class, ['getCountry']);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = $this->get(OrderArticleChecker::class);

        $this->assertTrue($oChecker->isValid());

        return $oChecker;
    }

    /**
     * Checks articles if valid.
     *
     * @return OrderArticleChecker
     */
    public function testCheckingArticlesWhenIncorrectArticlesExistsButCountryIsEuButNotTBE()
    {
        $oCountry = $this->createPartialMock(Country::class, ['isInEU', 'appliesOeTBEVATTbeVat']);
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(false));

        $oUser = $this->createPartialMock(User::class, ['getCountry']);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = $this->get(OrderArticleChecker::class);

        $this->assertTrue($oChecker->isValid());

        return $oChecker;
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
    protected function _createArticle($blTBEService, $iVat, $sId = null)
    {
        $oArticle = $this->createPartialMock(Article::class, ['isOeVATTBETBEService', 'getOeVATTBETBEVat', 'getId']);
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->will($this->returnValue($blTBEService));
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->will($this->returnValue($iVat));
        if (!is_null($sId)) {
            $oArticle->expects($this->any())->method('getId')->will($this->returnValue($sId));
        }

        return $oArticle;
    }
}
