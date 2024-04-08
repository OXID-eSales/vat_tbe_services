<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Article;

use OxidEsales\Eshop\Application\Controller\Admin\ArticleList as ArticleListController;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Shop\ArticleList;
use OxidEsales\EVatModule\Shop\User;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Testing extended Article class.
 */
class ArticleListTest extends BaseTestCase
{
    /**
     * data provider
     *
     * @return array
     */
    public static function userConfiguration(): array
    {
        return [
            ['notLoggedIn', null],
            ['loggedIn', '6.00'],
            ['loggedInWithoutCountry', null]
        ];
    }

    /**
     * Category list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testCategoryList($sUserStatus, $sVat): void
    {
        $oArticleList = $this->_getArticleList($sUserStatus);
        $sessionFilter = Registry::getSession()->getVariable('session_attrfilter');
        $oArticleList->loadCategoryArticles('30e44ab8593023055.23928895', $sessionFilter, null);

        /** @var Article $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf(Article::class, $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Category list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testManufacturerList($sUserStatus, $sVat)
    {
        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadManufacturerArticles('manufacturerId');

        /** @var Article $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf(Article::class, $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Category list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testVendorList($sUserStatus, $sVat)
    {
        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadVendorArticles('vendorId');

        /** @var Article $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf(Article::class, $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Category list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testPriceCategoryList($sUserStatus, $sVat)
    {
        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadPriceArticles(20, 40);

        /** @var Article $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf(Article::class, $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Action list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testActionList($sUserStatus, $sVat)
    {
        $oArticle2Action = oxNew(BaseModel::class);
        $oArticle2Action->init('oxactions2article');
        $oArticle2Action->assign([
            'oxactionid' => 'oxevatdemo',
            'oxartid'    => '1126',
        ]);
        $oArticle2Action->save();

        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadActionArticles('oxevatdemo');

        /** @var Article $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf(Article::class, $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Newest list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testNewestList($sUserStatus, $sVat)
    {
        Registry::getConfig()->setConfigParam('iNewestArticlesMode', 2);
        Registry::getConfig()->setConfigParam('blNewArtByInsert', false);

        $oArticle = oxNew(Article::class);
        $oArticle->setId('1126');
        $oArticle->assign([
            'oxtimestamp' => date('Y-m-d H:i:s', Registry::getUtilsDate()->getTime())
        ]);
        $oArticle->save();

        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadNewestArticles();

        /** @var Article $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf(Article::class, $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Top 5 list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testTop5Articles($sUserStatus, $sVat)
    {
        Registry::getConfig()->setConfigParam('iTop5Mode', 2);

        $oArticle = oxNew(Article::class);
        $oArticle->setId('1126');
        $oArticle->assign([
            'oxsoldamount' => 9999
        ]);
        $oArticle->save();

        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadTop5Articles();

        /** @var Article $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf(Article::class, $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Cross selling list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testArticleCrossSell($sUserStatus, $sVat)
    {
        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadArticleCrossSell('1964');

        /** @var Article $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf(Article::class, $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Article accessoires list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testArticleAccessoires($sUserStatus, $sVat)
    {
        $oAccessoire2article = oxNew(BaseModel::class);
        $oAccessoire2article->init("oxaccessoire2article");
        $oAccessoire2article->assign([
            'oxobjectid'   => '1126',
            'oxarticlenid' => '1964',
        ]);
        $oAccessoire2article->save();

        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadArticleAccessoires('1964');

        /** @var Article $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf(Article::class, $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Recommendation list test case
     *
     * @param string $sUserStatus user status
     * @param string $sVat        vat value
     *
     * @dataProvider userConfiguration
     */
    public function testloadRecommArticles($sUserStatus, $sVat)
    {
        $oObject2list = oxNew(BaseModel::class);
        $oObject2list->init("oxobject2list");
        $oObject2list->assign([
            'oxobjectid' => '1126',
            'oxlistid'   => 'list',
        ]);
        $oObject2list->save();

        $oArticleList = $this->_getArticleList($sUserStatus);
        $oArticleList->loadRecommArticles('list');

        /** @var Article $oArticle */
        $oArticle = $oArticleList['1126'];

        $this->assertInstanceOf(Article::class, $oArticle);
        $this->assertSame($sVat, $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Test forming of articles list in admin when user country is found but shop is in admin mode.
     */
    public function testLoadingArticlesInAdminWithFilter()
    {
        Registry::getSession()->setVariable('TBECountryId', 'a7c40f631fc920687.20179984');
        Registry::getConfig()->setConfigParam('art_category', 'cat@@30e44ab8593023055.23928895');
        Registry::getSession()->setUser(null);

        $oArticleList = oxNew(ArticleListController::class);
        $oArticleList->setAdminMode(true);
        $aListItems = $oArticleList->getItemList();

        $this->assertSame(4, count($aListItems));
    }

    /**
     * Prepare article list object for testing
     *
     * @param string $sUserStatus user status
     *
     * @return ArticleList
     */
    protected function _getArticleList($sUserStatus = 'notLoggedIn')
    {
        $oArticleList = oxNew(ArticleList::class);
        $oArticle = $oArticleList->getBaseObject();

        if ($sUserStatus != 'notLoggedIn') {
            $oUser = $this->getMockBuilder(User::class)
                ->onlyMethods(array("getOeVATTBETbeCountryId"))
                ->getMock();
            $sCountryId = ($sUserStatus == 'loggedInWithoutCountry') ? null : 'a7c40f631fc920687.20179984';
            $oUser->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue($sCountryId));
            $oArticle->setUser($oUser);
        }

        return $oArticleList;
    }
}
