<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Shop;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\EVatModule\Shop\VatSelector;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\Eshop\Application\Model\User;

/**
 * Testing extended oxUser class.
 */
#[AllowMockObjectsWithoutExpectations]
class VatSelectorTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Registry::getSession()->setAdminMode(false);
        Registry::getConfig()->setAdminMode(false);
        Registry::getSession()->setUser(null);
    }

    public static function providerArticleUserVatCalculationWhenHasTbeVatAndIsTbeArticle(): array
    {
        return [
            [100],
            [19],
            [0],
            [-25],
        ];
    }

    /**
     * @param int $iVat
     */
    #[DataProvider('providerArticleUserVatCalculationWhenHasTbeVatAndIsTbeArticle')]
    public function testArticleUserVatCalculationWhenHasTbeVatAndIsTbeArticle($iVat)
    {
        $oArticle = $this->createPartialMock(Article::class, ['getOeVATTBETBEVat', 'isOeVATTBETBEService']);
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->willReturn($iVat);
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->willReturn(true);

        $oVatSelector = oxNew(VatSelector::class);

        $this->assertSame($iVat, $oVatSelector->getArticleUserVat($oArticle));
    }

    /**
     * When article does not have TBE VAT calculated but is TBE article, it should fall back
     * to parent's VAT calculation. As in this case user is not logged in - false is returned.
     */
    public function testArticleUserVatCalculationWhenTbeVatNotSetAndIsTbeArticle()
    {
        $oArticle = $this->createPartialMock(Article::class, ['getOeVATTBETBEVat', 'isOeVATTBETBEService']);
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->willReturn(null);
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->willReturn(true);

        $oVatSelector = oxNew(VatSelector::class);

        $this->assertSame(false, $oVatSelector->getArticleUserVat($oArticle));
    }

    /**
     * When article does have TBE VAT calculated but is not TBE article, it should fall back
     * to parent's VAT calculation. As in this case user is not logged in - false is returned.
     */
    public function testArticleUserVatCalculationWhenTbeVatSetAndIsNotTbeArticle()
    {
        $oArticle = $this->createPartialMock(Article::class, ['getOeVATTBETBEVat', 'isOeVATTBETBEService']);
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->willReturn(15);
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->willReturn(false);

        $oVatSelector = oxNew(VatSelector::class);

        $this->assertSame(false, $oVatSelector->getArticleUserVat($oArticle));
    }

    /**
     * Test VAT calculation when article is TBE service, but there is admin mode tuned on.
     */
    public function testArticleUserVatCalculationWhenIsAdmin()
    {
        $oArticle = $this->createPartialMock(Article::class, ['getOeVATTBETBEVat', 'isOeVATTBETBEService']);
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->willReturn(15);
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->willReturn(true);

        Registry::getSession()->setAdminMode(true);
        Registry::getConfig()->setAdminMode(true);

        $oVatSelector = oxNew(VatSelector::class);

        $this->assertSame(false, $oVatSelector->getArticleUserVat($oArticle));

        Registry::getSession()->setAdminMode(false);
        Registry::getConfig()->setAdminMode(false);
    }

    /**
     * Test VAT calculation when article is TBE service, but user is from shop's domestic country.
     */
    public function testArticleUserVatCalculationWhenUserFromDomesticCountry()
    {
        ContainerFacade::get(ModuleSettings::class)->saveDomesticCountry('DE');
        Registry::getSession()->setVariable('TBECountryId', 'a7c40f631fc920687.20179984');
        $oArticle = $this->createPartialMock(Article::class, ['getOeVATTBETBEVat', 'isOeVATTBETBEService']);
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->willReturn(15);
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->willReturn(true);

        $oVatSelector = oxNew(VatSelector::class);

        $this->assertSame(false, $oVatSelector->getArticleUserVat($oArticle));
    }

    /**
     * Test VAT calculation when article is TBE service and user is not from shop's domestic country.
     */
    public function testArticleUserVatCalculationWhenUserNotFromDomesticCountry()
    {
        ContainerFacade::get(ModuleSettings::class)->saveDomesticCountry('LT');
        Registry::getSession()->setVariable('TBECountryId', 'a7c40f631fc920687.20179984');
        $oArticle = $this->createPartialMock(Article::class, ['getOeVATTBETBEVat', 'isOeVATTBETBEService']);
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->willReturn(15);
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->willReturn(true);

        $oVatSelector = oxNew(VatSelector::class);

        $this->assertSame(15, $oVatSelector->getArticleUserVat($oArticle));
    }

    /**
     * Test if VAT applied for not business customer
     * when article is TBE service and user is not from shop's domestic country.
     */
    public function testGetArticleForNotBusinessCustomer()
    {
        /** @var User $oUser */
        $oUser = oxNew(User::class);
        $oUser->oxuser__oxustid = new Field('0');

        ContainerFacade::get(ModuleSettings::class)->saveDomesticCountry('LT');
        Registry::getSession()->setVariable('TBECountryId', 'a7c40f631fc920687.20179984');
        $oArticle = $this->createPartialMock(Article::class, ['getOeVATTBETBEVat', 'isOeVATTBETBEService']);
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->willReturn(15);
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->willReturn(true);
        $oArticle->setArticleUser($oUser);

        /** @var VatSelector $oVatSelector */
        $oVatSelector = oxNew(VatSelector::class);

        $this->assertSame(15, $oVatSelector->getArticleUserVat($oArticle));
    }

    /**
     * Test if 0 VAT applied for business customer
     * when article is TBE service and user is not from shop's domestic country.
     */
    public function testGetArticleForBusinessCustomer()
    {
        /** @var User $oUser */
        $oUser = oxNew(User::class);
        $oUser->oxuser__oxustid = new Field('1');

        ContainerFacade::get(ModuleSettings::class)->saveDomesticCountry('LT');
        Registry::getSession()->setVariable('TBECountryId', 'a7c40f631fc920687.20179984');
        $oArticle = $this->createPartialMock(Article::class, ['getOeVATTBETBEVat', 'isOeVATTBETBEService']);
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->willReturn(15);
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->willReturn(true);
        $oArticle->setArticleUser($oUser);

        /** @var VatSelector $oVatSelector */
        $oVatSelector = oxNew(VatSelector::class);

        $this->assertNotSame(15, $oVatSelector->getArticleUserVat($oArticle));
        $this->assertEquals(false, $oVatSelector->getArticleUserVat($oArticle));
    }
}
