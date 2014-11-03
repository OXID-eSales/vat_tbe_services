<?php
/**
 * This file is part of OXID eSales eVAT module.
 *
 * OXID eSales eVAT module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales eVAT module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales eVAT module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */


/**
 * Testing oeVATTBEBasket class.
 *
 * @covers oeVATTBEBasket
 * @covers oeVATTBEBasketVATValidator
 */
class Integration_oeVatTbe_Checkout_oeVATTBEBasketMarksTest extends OxidTestCase
{

    /**
     * data provider for test testShowVATTBEMark
     *
     * @return array
     */
    public function providerShowVATTBEMark()
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
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('sOeVATTBEDomesticCountry', 'AT');
        $oSession = $this->getSession();
        $oSession->setVariable('TBECountryId', '8f241f11095d6ffa8.86593236'); // LT

        /** @var oxCountry|oeVATTBEOxCountry $oCountry */
        $oCountry = oxNew('oxCountry');
        $oCountry->setId('_testCountry1');
        $oCountry->oxcountry__oevattbe_appliestbevat = new oxField($blIsCountryConfigured);
        $oCountry->save();

        /** @var oxArticle $oArticle */
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArticle1');
        $oArticle->oxarticles__oevattbe_istbeservice = new oxField($blIsArticleTbeService);
        $oArticle->save();

        /** @var oxUser|null $oUser */
        $oUser = ($blIsUserLoggedIn) ? oxNew('oxUser') : null;
        $oSession->setUser($oUser);

        /** @var oxBasket|oeVATTBEOxBasket $oBasket */
        $oBasket = oxNew('oxBasket');
        $oBasket->setOeVATTBECountryId('_testCountry1');

        $oSession->setBasket($oBasket);

        /** @var oeVATTBEBasket $oBasketController */
        $oBasketController = oxNew('oeVATTBEBasket');

        /** @var oxBasketItem $oBasketItem */
        $oBasketItem = oxNew('oxBasketItem');
        $oBasketItem->init('_testArticle1', 1);

        $this->assertSame($blResult, $oBasketController->oeVATTBEShowVATTBEMark($oBasketItem));
    }

    /**
     * data provider for test testIsTBEArticleValid
     *
     * @return array
     */
    public function providerIsTBEArticleValid()
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
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('sOeVATTBEDomesticCountry', 'AT');
        $oSession = $this->getSession();
        $oSession->setVariable('TBECountryId', '8f241f11095d6ffa8.86593236'); // LT

        /** @var oxCountry|oeVATTBEOxCountry $oCountry */
        $oCountry = oxNew('oxCountry');
        $oCountry->setId('_testCountry1');
        $oCountry->oxcountry__oevattbe_appliestbevat = new oxField(true);
        $oCountry->save();

        /** @var oxArticle $oArticle */
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArticle1');
        $oArticle->oxarticles__oevattbe_istbeservice = new oxField(true);
        $oArticle->save();

        /** @var oxUser|null $oUser */
        $oUser = oxNew('oxUser');
        $oSession->setUser($oUser);

        /** @var oxBasket|oeVATTBEOxBasket $oBasket */
        $oBasket = oxNew('oxBasket');
        if (!$blIsArticleValid) {
            $oBasket->addToBasket('_testArticle1', 1);
        }
        $oBasket->setOeVATTBECountryId('_testCountry1');

        $oSession->setBasket($oBasket);

        /** @var oeVATTBEBasket $oBasketController */
        $oBasketController = oxNew('oeVATTBEBasket');

        /** @var oxBasketItem $oBasketItem */
        $oBasketItem = oxNew('oxBasketItem');
        $oBasketItem->init('_testArticle1', 1);

        $this->assertSame($blResult, $oBasketController->isOeVATTBETBEArticleValid($oBasketItem));
    }
}
