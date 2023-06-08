<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Article;

use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\EVatModule\Shop\Search;
use OxidEsales\EVatModule\Shop\User;
use PHPUnit\Framework\TestCase;

/**
 * Testing extended Article class.
 */
class SearchTest extends TestCase
{
    /**
     * Search article list test
     */
    public function testGetSearchArticles()
    {
        $oUser = $this->getMockBuilder(User::class)
            ->onlyMethods(array("getOeVATTBETbeCountryId"))
            ->getMock();
        $oUser->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue('a7c40f631fc920687.20179984'));

        $oSearch = oxNew(Search::class);
        $oSearch->setUser($oUser);

        $oArticleList = $oSearch->getSearchArticles('ABSINTH');

        /** @var Article $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf(Article::class, $oArticle);
        $this->assertSame('6.00', $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Search article list test
     */
    public function testGetSearchArticlesUserCountryNotSet()
    {
        $oUser = $this->getMockBuilder(User::class)
            ->onlyMethods(array("getOeVATTBETbeCountryId"))
            ->getMock();
        $oUser->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue(null));

        $oSearch = oxNew(Search::class);
        $oSearch->setUser($oUser);

        $oArticleList = $oSearch->getSearchArticles('ABSINTH');

        /** @var Article $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertNull($oArticle->getOeVATTBETBEVat());
    }

    /**
     * Search article list test
     */
    public function testGetSearchArticlesUserNotLogged()
    {
        $oSearch = oxNew(Search::class);

        $oArticleList = $oSearch->getSearchArticles('ABSINTH');

        /** @var Article $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertNull($oArticle->getOeVATTBETBEVat());
    }
}
