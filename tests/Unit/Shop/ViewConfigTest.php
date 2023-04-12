<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Shop;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\Eshop\Application\Model\Article as EShopArticle;
use OxidEsales\EVatModule\Shop\ViewConfig;
use OxidEsales\Eshop\Core\ViewConfig as EShopViewConfig;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxViewConfig class.
 *
 * @covers ViewConfig
 */
class ViewConfigTest extends TestCase
{
    /**
     * User is not from domestic country;
     * TBE article is given;
     * Notice should be shown.
     */
    public function testShowTBEArticlePriceNoticeWhenUserIsNotFromDomesticCountry()
    {
        Registry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');
        Registry::getSession()->setVariable('TBECountryId', '8f241f11095d6ffa8.86593236'); // LT

        /** @var Article|EShopArticle|MockObject $oArticle */
        $oArticle = $this->createStub(Article::class);
        $oArticle->method('isOeVATTBETBEService')->willReturn(true);

        /** @var ViewConfig|EShopViewConfig $oViewConfig */
        $oViewConfig = oxNew(ViewConfig::class);

        $this->assertTrue($oViewConfig->oeVATTBEShowTBEArticlePriceNotice($oArticle));
    }

    /**
     * User is from domestic country;
     * TBE article is given;
     * Notice should not be shown.
     */
    public function testShowTBEArticlePriceNoticeWhenUserIsFromDomesticCountry()
    {
        Registry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');
        Registry::getSession()->setVariable('TBECountryId', 'a7c40f631fc920687.20179984'); // DE

        /** @var Article|EShopArticle|MockObject $oArticle */
        $oArticle = $this->createStub(Article::class);
        $oArticle->method('isOeVATTBETBEService')->willReturn(true);

        /** @var ViewConfig|EShopViewConfig $oViewConfig */
        $oViewConfig = oxNew(ViewConfig::class);

        $this->assertFalse($oViewConfig->oeVATTBEShowTBEArticlePriceNotice($oArticle));
    }

    /**
     * User is not from domestic country;
     * Non TBE article is given;
     * Notice should not be shown.
     */
    public function testShowTBEArticlePriceNoticeWhenArticleIsNotTBE()
    {
        Registry::getConfig()->setConfigParam('sOeVATTBEDomesticCountry', 'DE');
        Registry::getSession()->setVariable('TBECountryId', '8f241f11095d6ffa8.86593236'); // LT

        /** @var Article|EShopArticle|MockObject $oArticle */
        $oArticle = $this->createStub(Article::class);
        $oArticle->method('isOeVATTBETBEService')->willReturn(false);

        /** @var ViewConfig|EShopViewConfig $oViewConfig */
        $oViewConfig = oxNew(ViewConfig::class);

        $this->assertFalse($oViewConfig->oeVATTBEShowTBEArticlePriceNotice($oArticle));
    }
}
