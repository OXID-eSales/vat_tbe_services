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
    /**
     * Prepare articles data: set articles to be TBE.
     */
    public function setUp()
    {
        parent::setup();
        $this->_prepareArticlesData();
    }

    /**
     * Provides with articles to check if error message is formed.
     *
     * @return array
     */
    public function providerMessageSetInBasketForAllArticlesWhenUserIsNotLeggedIn()
    {
        $sIdTbeArticleWithVatGroup = '1126';
        $sTbeArticleWithoutVatGroup = '1127';

        $sErrorMessage1 = '/.*: Bar-Set ABSINTH.*/';
        $sErrorMessage2 = '/.*: Blinkende Eisw.*/';
        $sErrorMessage3 = '/.*: Bar-Set ABSINTH, Blinkende Eisw.*/';
        return array(
            array(array($sIdTbeArticleWithVatGroup), $sErrorMessage1),
            array(array($sTbeArticleWithoutVatGroup), $sErrorMessage2),
            array(array($sIdTbeArticleWithVatGroup, $sTbeArticleWithoutVatGroup), $sErrorMessage3),
        );
    }

    /**
     * Check if message is set in first checkout step for all TBE articles when user is not logged in.
     *
     * @param array  $aArticles     array of articles to set to basket and check.
     * @param string $sErrorMessage article names which should be displayed in error message.
     *
     * @dataProvider providerMessageSetInBasketForAllArticlesWhenUserIsNotLeggedIn
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
        $this->assertRegExp($sErrorMessage, $aEx['default'][0], 'Error message: '. $aEx['default'][0]);
    }

    /**
     * Provides with articles to check if error message is formed.
     *
     * @return array
     */
    public function providerMessageSetInBasketForWrongVATArticlesWhenUserIsLeggedIn()
    {
        $sIdTbeArticleWithVatGroup = '1126';
        $sTbeArticleWithoutVatGroup = '1127';

        $sErrorMessage2 = '/.*: Blinkende Eisw.*/';
        $sErrorMessage3 = '/.*: Blinkende Eisw.*/';
        return array(
            array(array($sTbeArticleWithoutVatGroup), $sErrorMessage2),
            array(array($sIdTbeArticleWithVatGroup, $sTbeArticleWithoutVatGroup), $sErrorMessage3),
        );
    }

    /**
     * Check if message is set in first checkout step for TBE articles with wrong VAT when user is logged in.
     *
     * @param array  $aArticles     array of articles to set to basket and check.
     * @param string $sErrorMessage article names which should be displayed in error message.
     *
     * @dataProvider providerMessageSetInBasketForWrongVATArticlesWhenUserIsLeggedIn
     */
    public function testMessageSetInBasketForWrongVATArticlesWhenUserIsLeggedIn($aArticles, $sErrorMessage)
    {
        $oSession = oxRegistry::getSession();

        /** @var oxBasket $oBasket */
        $oBasket = oxNew('oxBasket');

        $oUser = $this->_createUser();
        $blLogin = $oUser->login($this->_sDefaultUserName, $this->_sDefaultPassword);
        $this->assertTrue($blLogin, 'User must login successfully.');
        $oSession->setUser($oUser);

        foreach ($aArticles as $sArticleId) {
            $oBasket->addToBasket($sArticleId, 1);
        }

        $oSession->setBasket($oBasket);

        /** @var basket $oBasket */
        $oBasket = oxNew('basket');
        $oBasket->render();

        $aEx = oxRegistry::getSession()->getVariable('Errors');
        $this->assertTrue(isset($aEx['default'][0]));
        $this->assertRegExp($sErrorMessage, $aEx['default'][0], 'Error message: '. $aEx['default'][0]);
    }

    /**
     * Prepare articles data.
     */
    protected function _prepareArticlesData()
    {
        $oDb = oxDb::getDb();

        $oDb->execute("TRUNCATE TABLE oevattbe_countryvatgroups");
        $oDb->execute("TRUNCATE TABLE oevattbe_articlevat");

        $sql = "INSERT INTO oevattbe_countryvatgroups SET OEVATTBE_ID = 1, OEVATTBE_COUNTRYID = '{$this->_sAustriaId}', OEVATTBE_NAME='name', OEVATTBE_RATE='8'";

        $oDb->execute($sql);

        $sql = "INSERT INTO oevattbe_articlevat SET OEVATTBE_ARTICLEID = '1126', OEVATTBE_COUNTRYID = '{$this->_sAustriaId}', OEVATTBE_VATGROUPID = '1'";

        $oDb->execute($sql);

        $sql = "UPDATE oxarticles SET oevattbe_istbeservice = '1' WHERE oxid in ('1126', '1127')";

        $oDb->execute($sql);
    }

    /**
     * Create demo user to test TBE articles for logged in user.
     *
     * @return oxUser
     */
    private function _createUser()
    {
        $sUserName = $this->_sDefaultUserName;
        $sEncodedPassword = $this->_sNewEncodedPassword;
        $sSalt = $this->_sNewSalt;
        $sGermanyId = $this->_sAustriaId;

        /** @var oxUser $oUser */
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxusername = new oxField($sUserName, oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField($sEncodedPassword, oxField::T_RAW);
        $oUser->oxuser__oxpasssalt = new oxField($sSalt, oxField::T_RAW);
        $oUser->oxuser__oxcountryid = new oxField($sGermanyId, oxField::T_RAW);
        $oUser->save();

        return $oUser;
    }

    /** @var string */
    protected $_sDefaultUserName = '_testUserName@oxid-esales.com';

    /** @var string */
    protected $_sDefaultUserPassword = '_testPassword';

    /** @var string encoded default password */
    private $_sNewEncodedPassword = 'b016e37ac8ec71449b475e84a941e3c39a27fb8f0710d4b47d6116ad6a6afcaa0c17006a4c01ffc67f3db95772fe001584cb4ce7e5bacd74198c24d1851841d5';

    /** @var string Salt generated with new algorithm. */
    private $_sNewSalt = '56784f8ffc657fff84915b93e12a626e';

    /** @var string Austria ID. */
    private $_sAustriaId = 'a7c40f6320aeb2ec2.72885259';
}
