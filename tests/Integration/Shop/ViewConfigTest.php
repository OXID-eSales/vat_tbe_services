<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Shop;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\Eshop\Application\Model\Article as EShopArticle;
use OxidEsales\EVatModule\Shop\ViewConfig;
use OxidEsales\Eshop\Core\ViewConfig as EShopViewConfig;
use OxidEsales\EVatModule\Traits\ServiceContainer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Testing extended oxViewConfig class.
 */
class ViewConfigTest extends TestCase
{
    use ServiceContainer;

    /**
     * User is not from domestic country;
     * TBE article is given;
     * Notice should be shown.
     */
    public function testShowTBEArticlePriceNoticeWhenUserIsNotFromDomesticCountry()
    {
        $this->getServiceFromContainer(ModuleSettings::class)->saveDomesticCountry('DE');
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
        Registry::getSession()->setVariable('TBECountryId', 'a7c40f631fc920687.20179984'); // DE
        $this->getServiceFromContainer(ModuleSettings::class)->saveDomesticCountry('DE');

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
        Registry::getSession()->setVariable('TBECountryId', '8f241f11095d6ffa8.86593236'); // LT
        $this->getServiceFromContainer(ModuleSettings::class)->saveDomesticCountry('DE');

        /** @var Article|EShopArticle|MockObject $oArticle */
        $oArticle = $this->createStub(Article::class);
        $oArticle->method('isOeVATTBETBEService')->willReturn(false);

        /** @var ViewConfig|EShopViewConfig $oViewConfig */
        $oViewConfig = oxNew(ViewConfig::class);

        $this->assertFalse($oViewConfig->oeVATTBEShowTBEArticlePriceNotice($oArticle));
    }
}
