<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Checkout;

use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EVatModule\Controller\BasketController;
use OxidEsales\EVatModule\Controller\OrderController;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Shop\User;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\EVatModule\Shop\Basket;
use OxidEsales\EVatModule\Shop\Country;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * Testing BasketController class.
 */
class OrderMarksTest extends BaseTestCase
{
    use ServiceContainer;

    public function setUp(): void
    {
        parent::setUp();

        ContainerFactory::resetContainer();
    }

    /**
     * data provider for test testShowVATTBEMark
     *
     * @return array
     */
    public static function providerShowVATTBEMark()
    {
        return array(
            array(true, true, true, true),
            array(false, true, true, true),
            array(false, true, false, true),
            array(true, false, true, false),
            array(false, false, true, false),
            array(true, true, false, false),
            array(true, false, false, false),
            array(false, false, false, false),
        );
    }

    /**
     * Basket Vat Validator test for oeVATTBEShowVATTBEMark method.
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
        $this->getServiceFromContainer(ModuleSettings::class)->saveDomesticCountry('AT');

        $oSession = Registry::getSession();
        $oSession->setVariable('TBECountryId', '8f241f11095d6ffa8.86593236'); // LT

        /** @var Country $oCountry */
        $oCountry = oxNew(Country::class);
        $oCountry->setId('_testCountry1');
        $oCountry->assign([
            'oevattbe_appliestbevat' => $blIsCountryConfigured
        ]);
        $oCountry->save();

        /** @var Article $oArticle */
        $oArticle = oxNew(Article::class);
        $oArticle->setId('_testArticle1');
        $oArticle->assign([
            'oevattbe_istbeservice' => $blIsArticleTbeService
        ]);
        $oArticle->save();

        /** @var User|null $oUser */
        $oUser = ($blIsUserLoggedIn) ? oxNew(User::class) : null;
        $oSession->setUser($oUser);

        /** @var Basket $oBasket */
        $oBasket = oxNew(Basket::class);
        $oBasket->setOeVATTBECountryId('_testCountry1');

        $oSession->setBasket($oBasket);

        /** @var OrderController $oOrderController */
        $oOrderController = oxNew(OrderController::class);

        /** @var BasketItem $oBasketItem */
        $oBasketItem = oxNew(BasketItem::class);
        $oBasketItem->init('_testArticle1', 1);

        $this->assertSame($blResult, $oOrderController->oeVATTBEShowVATTBEMark($oBasketItem));
    }

    /**
     * data provider for test testIsTBEArticleValid
     *
     * @return array
     */
    public static function providerIsTBEArticleValid()
    {
        return array(
            array(false, false),
            array(true, true),
        );
    }

    /**
     * Basket Vat Validator test for isOeVATTBETBEArticleValid method.
     *
     * @param bool   $blIsArticleValid Article is valid / invalid
     * @param string $blResult         Expected value
     *
     * @dataProvider providerIsTBEArticleValid
     */
    public function testIsTBEArticleValid($blIsArticleValid, $blResult)
    {
        $this->getServiceFromContainer(ModuleSettings::class)->saveDomesticCountry('AT');

        $oSession = Registry::getSession();
        $oSession->setVariable('TBECountryId', '8f241f11095d6ffa8.86593236'); // LT

        /** @var Country $oCountry */
        $oCountry = oxNew(Country::class);
        $oCountry->setId('_testCountry1');
        $oCountry->assign([
            'oevattbe_appliestbevat' => true
        ]);
        $oCountry->save();

        /** @var Article $oArticle */
        $oArticle = oxNew(Article::class);
        $oArticle->setId('_testArticle1');
        $oArticle->assign([
            'oevattbe_istbeservice' => true
        ]);
        $oArticle->save();

        /** @var User|null $oUser */
        $oUser = oxNew(User::class);
        $oSession->setUser($oUser);

        /** @var Basket $oBasket */
        $oBasket = oxNew(Basket::class);
        if (!$blIsArticleValid) {
            $oBasket->addToBasket('_testArticle1', 1);
        }
        $oBasket->setOeVATTBECountryId('_testCountry1');

        $oSession->setBasket($oBasket);

        /** @var BasketController $oOrderController */
        $oOrderController = oxNew(BasketController::class);

        /** @var BasketItem $oBasketItem */
        $oBasketItem = oxNew(BasketItem::class);
        $oBasketItem->init('_testArticle1', 1);

        $this->assertSame($blResult, $oOrderController->isOeVATTBETBEArticleValid($oBasketItem));
    }
}
