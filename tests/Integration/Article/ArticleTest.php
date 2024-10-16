<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Article;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Shop\User;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;
use OxidEsales\Facts\Facts;

/**
 * Testing extended oxArticle class.
 */
class ArticleTest extends BaseTestCase
{
    /**
     * Test case for loading article
     */
    public function testLoadArticle()
    {
        Registry::getSession()->setVariable('TBECountryId', 'a7c40f631fc920687.20179984');
        $oUser = $this->getMockBuilder(User::class)
            ->onlyMethods(array("getOeVATTBETbeCountryId"))
            ->getMock();
        $oUser->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue('a7c40f631fc920687.20179984'));

        $oArticle = oxNew(Article::class);
        $oArticle->setAdminMode(false);
        $oArticle->setUser($oUser);

        $oArticle->load('1126');

        $this->assertSame('6.00', $oArticle->getOeVATTBETBEVat());
    }

    /**
     * Test case for loading article
     */
    public function testLoadArticleUserIsFromLocalCountry()
    {
        $oUser = $this->getMockBuilder(User::class)
            ->onlyMethods(array("getOeVATTBETbeCountryId"))
            ->getMock();
        $oUser->expects($this->any())->method("getOeVATTBETbeCountryId")->will($this->returnValue(null));

        $oArticle = oxNew(Article::class);
        $oArticle->setUser($oUser);

        $oArticle->load('1126');

        $this->assertNull($oArticle->getOeVATTBETBEVat());
    }

    /**
     * Test case for loading article
     */
    public function testLoadArticleUserNotLoggedIn()
    {
        $oArticle = oxNew(Article::class);
        $oArticle->load('1126');

        $this->assertNull($oArticle->getOeVATTBETBEVat());
    }

    /**
     * Test that module does not change behaviour when user is not logged in.
     */
    public function testGetCacheKeysWithoutActiveUser()
    {
        if (!(new Facts())->isEnterprise()) {
            $this->markTestSkipped('Test only on Enterprise shop');
        }

        $oArticleWithoutModules = new Article();
        $aCacheKeysWithoutModules = $oArticleWithoutModules->getCacheKeys();

        /** @var Article $oArticle */
        $oArticle = oxNew(Article::class);
        $aCacheKeys = $oArticle->getCacheKeys();
        $this->assertSame($aCacheKeysWithoutModules, $aCacheKeys);
    }

    /**
     * Test that module does not add user country for not TBE article when user is logged in.
     */
    public function testGetCacheKeysForNotTbeArticleWithActiveUser()
    {
        if (!(new Facts())->isEnterprise()) {
            $this->markTestSkipped('Test only on Enterprise shop');
        }

        $sAustriaId = 'a7c40f6320aeb2ec2.72885259';
        /** @var User $oUser */
        $oUser = $this->getMockBuilder(User::class)
            ->onlyMethods(array("getOeVATTBETbeCountryId"))
            ->getMock();
        $oUser->expects($this->any())->method('getOeVATTBETbeCountryId')->will($this->returnValue($sAustriaId));

        /** @var Article $oArticle */
        $oArticle = oxNew(Article::class);
        $oArticle->setUser($oUser);
        $aCacheKeys = $oArticle->getCacheKeys();

        $sShopId = Registry::getConfig()->getShopId();
        $this->assertSame(['oxArticle__' . $sShopId . '_de', 'oxArticle__' . $sShopId . '_en'], $aCacheKeys);
    }

    /**
     * Test that module add user country for TBE article when user is logged in.
     */
    public function testGetCacheKeysForTbeArticleWithActiveUser()
    {
        if (!(new Facts())->isEnterprise()) {
            $this->markTestSkipped('Test only on Enterprise shop');
        }

        $sAustriaId = 'a7c40f6320aeb2ec2.72885259';
        /** @var User $oUser */
        $oUser = $this->getMockBuilder(User::class)
            ->onlyMethods(array("getOeVATTBETbeCountryId"))
            ->getMock();
        $oUser->expects($this->any())->method('getOeVATTBETbeCountryId')->will($this->returnValue($sAustriaId));

        /** @var Article $oArticle */
        $oArticle = oxNew(Article::class);
        $oArticle->setUser($oUser);
        $oArticle->assign([
            'oevattbe_istbeservice' => true,
            'oxarticles__oxstock'   => 999,
            'oxshopid'              => 1,
            'oxparentid'            => '',
            'oxstockflag'           => 1,
            'oxstock'               => 999,
            'oxvarstock'            => 999,
            'oxvarcount'            => 999,
        ]);
        $aCacheKeys = $oArticle->getCacheKeys();

        $sShopId = Registry::getConfig()->getShopId();
        $this->assertSame(['oxArticle__' . $sShopId . '_de_' . $sAustriaId, 'oxArticle__' . $sShopId . '_en_' . $sAustriaId], $aCacheKeys);
    }
}
