<?php
/**
 * This file is part of OXID eSales VAT TBE module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales VAT TBE module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */


/**
 * Testing message in checkout process for TBE articles with wrong VAT.
 */
class Integration_oeVatTbe_checkout_oeVATTBEMessageForWrongTBEVatTest extends OxidTestCase
{
    public function setUp()
    {
        parent::setup();
        $this->_prepareData();
    }

    public function providerMessageSetInBasketPageWhenTBEArticleWithWrongVATExistInBasket()
    {
        $sIdTbeArticleWithVatGroup = '1126';
        $sTbeArticleWithoutVatGroup = '1127';

        $sErrorMessage1 = '/.*Bar-Set ABSINTH.*/';
        $sErrorMessage2 = '/.*Blinkende Eisw.*/';
        $sErrorMessage3 = '/.*Bar-Set ABSINTH, Blinkende Eisw.*/';
        return array(
            array(array($sIdTbeArticleWithVatGroup), $sErrorMessage1),
            array(array($sTbeArticleWithoutVatGroup), $sErrorMessage2),
            array(array($sIdTbeArticleWithVatGroup, $sTbeArticleWithoutVatGroup), $sErrorMessage3),
        );
    }

    /**
     * Check if message is set in first checkout step for all TBE articles when user is logged in.
     *
     * @param array $aArticles array of articles to set to basket and check.
     *
     * @dataProvider providerMessageSetInBasketPageWhenTBEArticleWithWrongVATExistInBasket
     */
    public function testMessageSetInBasketForAllArticlesWhenUserIsNotLeggedIn($aArticles, $sErrorMessage)
    {
        /** @var oxBasket $oBasket */
        $oBasket = oxNew('oxBasket');
        foreach ($aArticles as $sArticleId) {
            $oBasket->addToBasket($sArticleId, 1);
        }

        $oSession = oxRegistry::getSession();
        $oSession->setBasket($oBasket);

        /** @var basket $oBasket */
        $oBasket = oxNew('basket');
        $oBasket->render();

        $aEx = oxRegistry::getSession()->getVariable('Errors');
        $this->assertTrue(isset($aEx['default'][0]));
        $this->assertRegExp($sErrorMessage, $aEx['default'][0]);
    }


    /**
     * prepare data
     */
    protected function _prepareData()
    {
        $oDb = oxDb::getDb();

        $oDb->execute("TRUNCATE TABLE oevattbe_countryvatgroups");
        $oDb->execute("TRUNCATE TABLE oevattbe_articlevat");

        $sql = "INSERT INTO oevattbe_countryvatgroups SET OEVATTBE_ID = 1, OEVATTBE_COUNTRYID = 'a7c40f631fc920687.20179984', OEVATTBE_NAME='name', OEVATTBE_RATE='8'";

        $oDb->execute($sql);

        $sql = "INSERT INTO oevattbe_articlevat SET OEVATTBE_ARTICLEID = '1126', OEVATTBE_COUNTRYID = 'a7c40f631fc920687.20179984', OEVATTBE_VATGROUPID = '1'";

        $oDb->execute($sql);

        $sql = "UPDATE oxarticles SET oevattbe_istbeservice = '1' WHERE oxid in ('1126', '1127')";

        $oDb->execute($sql);
    }
}
