<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Checkout;

use OxidEsales\Eshop\Core\Email;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EVatModule\Controller\BasketController;
use OxidEsales\EVatModule\Controller\OrderController;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Shop\Basket;
use OxidEsales\EVatModule\Shop\User;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Testing message in checkout process for TBE articles with wrong VAT.
 */
class CheckoutMessageTest extends BaseTestCase
{
    /**
     * Prepare articles data: set articles to be TBE.
     */
    public function setUp(): void
    {
        parent::setup();

        ContainerFactory::resetContainer();

        ContainerFacade::get(ModuleSettings::class)->saveDomesticCountry('DE');
    }

    /**
     * Provides with articles to check if error message is formed.
     *
     * @return array
     */
    public static function providerMessageSetInBasketForAllArticlesWhenUserIsNotLoggedIn()
    {
        $sIdTbeArticleWithVatGroup = '1126';
        $sTbeArticleWithoutVatGroup = '1127';
        $sNotTbeArticle = '1131';

        $sErrorMessage1 = '/.*: Bar-Set ABSINTH.*/';
        $sErrorMessage2 = '/.*: Blinkende Eisw.*/';
        $sErrorMessage3 = '/.*: Bar-Set ABSINTH, Blinkende Eisw.*/';
        return array(
            array(array($sIdTbeArticleWithVatGroup), $sErrorMessage1),
            array(array($sTbeArticleWithoutVatGroup), $sErrorMessage2),
            array(array($sIdTbeArticleWithVatGroup, $sTbeArticleWithoutVatGroup), $sErrorMessage3),
            array(array($sIdTbeArticleWithVatGroup, $sTbeArticleWithoutVatGroup, $sNotTbeArticle), $sErrorMessage3),
        );
    }

    /**
     * Check if message is not set in first checkout step for all TBE articles when user is not logged in.
     * No error message set for not TBE articles.
     *
     * @param array  $aArticles     array of articles to set to basket and check.
     * @param string $sErrorMessage article names which should be displayed in error message.
     *
     * @dataProvider providerMessageSetInBasketForAllArticlesWhenUserIsNotLoggedIn
     */
    public function testMessageSetInBasketForAllArticlesWhenUserIsNotLoggedIn($aArticles, $sErrorMessage)
    {
        /** @var Basket $oBasket */
        $oBasket = oxNew(Basket::class);
        foreach ($aArticles as $sArticleId) {
            $oBasket->addToBasket($sArticleId, 1);
        }

        $oSession = Registry::getSession();
        $oSession->setBasket($oBasket);
        $oSession->setUser(null);

        /** @var BasketController $oBasket */
        $oBasket = oxNew(BasketController::class);
        $oBasket->render();

        $aEx = Registry::getSession()->getVariable('Errors');
        $this->assertFalse(isset($aEx['default'][0]));
    }

    /**
     * Provides with articles to check if error message is formed.
     *
     * @return array
     */
    public static function providerMessageIsNotSetInBasketWhenUserIsNotLoggedIn()
    {
        $sNotTbeArticle = '1131';

        return array(
            array(array($sNotTbeArticle)),
        );
    }

    /**
     * Check if message is not set in first checkout step when all articles correct and user is not logged in.
     *
     * @param array $aArticles array of articles to set to basket and check.
     *
     * @dataProvider providerMessageIsNotSetInBasketWhenUserIsNotLoggedIn
     */
    public function testMessageIsNotSetInBasketWhenUserIsNotLoggedIn($aArticles)
    {
        /** @var Basket $oBasket */
        $oBasket = oxNew(Basket::class);
        foreach ($aArticles as $sArticleId) {
            $oBasket->addToBasket($sArticleId, 1);
        }

        $oSession = Registry::getSession();
        $oSession->setBasket($oBasket);

        /** @var BasketController $oBasket */
        $oBasket = oxNew(BasketController::class);
        $oBasket->render();

        $aEx = Registry::getSession()->getVariable('Errors');
        $this->assertFalse(isset($aEx['default'][0]));
    }

    /**
     * Provides with articles to check if error message is formed.
     *
     * @return array
     */
    public static function providerMessageSetInBasketForWrongVATArticlesWhenUserIsLoggedIn()
    {
        $sIdTbeArticleWithVatGroup = '1126';
        $sTbeArticleWithoutVatGroup = '1127';
        $sNotTbeArticle = '1131';

        $sErrorMessage2 = '/.*: ABSINTH.*/';
        $sErrorMessage3 = '/.*: ABSINTH.*/';
        return array(
            array(array($sTbeArticleWithoutVatGroup), $sErrorMessage2),
            array(array($sIdTbeArticleWithVatGroup, $sTbeArticleWithoutVatGroup), $sErrorMessage3),
            array(array($sIdTbeArticleWithVatGroup, $sTbeArticleWithoutVatGroup, $sNotTbeArticle), $sErrorMessage3),
        );
    }

    /**
     * Check if message is set in first checkout step for TBE articles with wrong VAT when user is logged in.
     *
     * @param array  $aArticles     array of articles to set to basket and check.
     * @param string $sErrorMessage article names which should be displayed in error message.
     *
     * @dataProvider providerMessageSetInBasketForWrongVATArticlesWhenUserIsLoggedIn
     */
    public function testMessageSetInBasketForWrongVATArticlesWhenUserIsLoggedIn($aArticles, $sErrorMessage)
    {
        $oSession = Registry::getSession();

        /** @var Basket $oBasket */
        $oBasket = oxNew(Basket::class);

        $oUser = $this->_createUser();
        $blLogin = $oUser->login($this->_sDefaultUserName, $this->_sDefaultUserPassword);
        $this->assertTrue($blLogin, 'User must login successfully.');
        $oSession->setUser($oUser);

        foreach ($aArticles as $sArticleId) {
            $oBasket->addToBasket($sArticleId, 1);
        }

        $oSession->setBasket($oBasket);

        /** @var BasketController $oBasket */
        $oBasketController = oxNew(BasketController::class);
        $oBasketController->render();

        $aEx = $oSession->getVariable('Errors');
        $this->assertTrue(isset($aEx['default'][0]));
        $this->assertMatchesRegularExpression($sErrorMessage, $aEx['default'][0], 'Error message: '. $aEx['default'][0]);
    }

    /**
     * TBE Article with no VAT calculated is added to basket;
     * User is logged in;
     * User country matches shops domestic country;
     * Basket controller is loaded;
     * Error message should not be shown.
     */
    public function testBasketErrorMessageNotSetForWrongVATArticlesWhenUserCountryIsDomestic()
    {
        $oSession = Registry::getSession();

        /** @var Basket $oBasket */
        $oBasket = oxNew(Basket::class);

        ContainerFacade::get(ModuleSettings::class)->saveDomesticCountry('AT');

        $oUser = $this->_createUser();
        $blLogin = $oUser->login($this->_sDefaultUserName, $this->_sDefaultUserPassword);
        $this->assertTrue($blLogin, 'User must login successfully.');
        $oSession->setUser($oUser);
        $oBasket->addToBasket('1127', 1);

        $oSession->setBasket($oBasket);

        /** @var BasketController $oBasket */
        $oBasketController = oxNew(BasketController::class);
        $oBasketController->render();

        $aEx = $oSession->getVariable('Errors');
        $this->assertFalse(isset($aEx['default'][0]));
    }

    /**
     * Provides with articles to check if error message is formed.
     *
     * @return array
     */
    public static function providerMessageIsNotSetInBasketWhenUserIsLoggedIn()
    {
        $sIdTbeArticleWithVatGroup = '1126';
        $sNotTbeArticle = '1131';

        return array(
            array(array($sIdTbeArticleWithVatGroup)),
            array(array($sIdTbeArticleWithVatGroup, $sNotTbeArticle)),
        );
    }

    /**
     * Check if message is not set in first checkout step when all articles are correct when user is logged in.
     *
     * @param array $aArticles array of articles to set to basket and check.
     *
     * @dataProvider providerMessageIsNotSetInBasketWhenUserIsLoggedIn
     */
    public function testMessageIsNotSetInBasketWhenUserIsLoggedIn($aArticles)
    {
        $oSession = Registry::getSession();

        /** @var Basket $oBasket */
        $oBasket = oxNew(Basket::class);

        $oUser = $this->_createUser();
        $blLogin = $oUser->login($this->_sDefaultUserName, $this->_sDefaultUserPassword);
        $this->assertTrue($blLogin, 'User must login successfully.');
        $oSession->setUser($oUser);

        foreach ($aArticles as $sArticleId) {
            $oBasket->addToBasket($sArticleId, 1);
        }

        $oSession->setBasket($oBasket);

        /** @var BasketController $oBasket */
        $oBasketController = oxNew(BasketController::class);
        $oBasketController->render();

        $aEx = $oSession->getVariable('Errors');
        $this->assertFalse(isset($aEx['default'][0]));
    }

    /**
     * Check if message is set in forth checkout step for TBE articles with wrong VAT.
     */
    public function testDoNotAllowOrderIfVATNotConfigured()
    {
        $_POST['stoken'] = 'stoken';
        $_POST['sDeliveryAddressMD5'] = 'b4ebffc0f1940d9a54599ec7e21d2f2c';

        $oSession = Registry::getSession();
        $oSession->setVariable('sess_stoken', 'stoken');
        $oSession->setVariable('TBECountryId', 'a7c40f6320aeb2ec2.72885259');

        /** @var Basket $oBasket */
        $oBasket = oxNew(Basket::class);
        $oBasket->setOeVATTBECountryId('a7c40f6320aeb2ec2.72885259');

        $oUser = $this->_createUser();
        $blLogin = $oUser->login($this->_sDefaultUserName, $this->_sDefaultUserPassword);
        $this->assertTrue($blLogin, 'User must login successfully.');
        $oSession->setUser($oUser);

        $aArticles = array('1126','1127');
        foreach ($aArticles as $sArticleId) {
            $oBasket->addToBasket($sArticleId, 1);
        }

        $oBasket->setBasketUser($oUser);
        $oBasket->setShipping('oxidstandard');
        $oBasket->setPayment('oxidpayadvance');
        $oBasket->calculateBasket(true);
        $oSession->setBasket($oBasket);

        $oOrder = oxNew(OrderController::class);
        $this->assertSame('order', $oOrder->execute());
    }

    /**
     * Check if message is set in forth checkout step for TBE articles with wrong VAT.
     */
    public function testAllowOrderIfVATConfigured()
    {
        $_POST['stoken'] = 'stoken';
        $_POST['sDeliveryAddressMD5'] = 'b4ebffc0f1940d9a54599ec7e21d2f2c';
        Registry::getConfig()->setConfigParam('sTheme', 'apex');
        ContainerFacade::get(ModuleSettings::class)->saveDomesticCountry('AT');

        /** @var Email $mailer */
        $mailer = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['send'])
            ->getMock();
        $mailer->expects($this->any())->method("send")->will($this->returnValue(true));

        $oSession = Registry::getSession();
        $oSession->setVariable('sess_stoken', 'stoken');
        $oSession->setVariable('TBECountryId', 'a7c40f6320aeb2ec2.72885259');

        /** @var Basket $oBasket */
        $oBasket = oxNew(Basket::class);
        $oBasket->setOeVATTBECountryId('a7c40f6320aeb2ec2.72885259');

        $oUser = $this->_createUser();
        $blLogin = $oUser->login($this->_sDefaultUserName, $this->_sDefaultUserPassword);
        $this->assertTrue($blLogin, 'User must login successfully.');
        $oSession->setUser($oUser);

        $aArticles = array('1126', '1131');
        foreach ($aArticles as $sArticleId) {
            $oBasket->addToBasket($sArticleId, 1);
        }

        $oBasket->setBasketUser($oUser);
        $oBasket->setShipping('oxidstandard');
        $oBasket->setPayment('oxidpayadvance');
        $oBasket->calculateBasket(true);
        $oSession->setBasket($oBasket);

        /** @var OrderController $oOrder */
        $oOrder = oxNew(OrderController::class);
        $this->assertSame('thankyou', $oOrder->execute());
    }

    /**
     * Check if message is set in forth checkout step for TBE articles with wrong VAT.
     */
    public function testAllowOrderIfNoTBEArticles()
    {
        $_POST['stoken'] = 'stoken';
        $_POST['sDeliveryAddressMD5'] = 'b4ebffc0f1940d9a54599ec7e21d2f2c';
        Registry::getConfig()->setConfigParam('sTheme', 'apex');
        ContainerFacade::get(ModuleSettings::class)->saveDomesticCountry('AT');

        /** @var Email $mailer */
        $mailer = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['send'])
            ->getMock();
        $mailer->expects($this->any())->method("send")->will($this->returnValue(true));

        $oSession = Registry::getSession();
        $oSession->setVariable('sess_stoken', 'stoken');
        $oSession->setVariable('TBECountryId', 'a7c40f6320aeb2ec2.72885259');

        /** @var Basket $oBasket */
        $oBasket = oxNew(Basket::class);
        $oBasket->setOeVATTBECountryId('a7c40f6320aeb2ec2.72885259');

        $oUser = $this->_createUser();
        $blLogin = $oUser->login($this->_sDefaultUserName, $this->_sDefaultUserPassword);
        $this->assertTrue($blLogin, 'User must login successfully.');
        $oSession->setUser($oUser);

        $aArticles = array('1131');
        foreach ($aArticles as $sArticleId) {
            $oBasket->addToBasket($sArticleId, 1);
        }

        $oBasket->setBasketUser($oUser);
        $oBasket->setShipping('oxidstandard');
        $oBasket->setPayment('oxidpayadvance');
        $oBasket->calculateBasket(true);
        $oSession->setBasket($oBasket);

        $oOrder = oxNew(OrderController::class);
        $this->assertSame('thankyou', $oOrder->execute());
    }

    /**
     * Create demo user to test TBE articles for logged in user.
     *
     * @return User
     */
    private function _createUser()
    {
        $sUserId = \oxDb::getDb()->getOne("SELECT `oxid` FROM `oxuser` WHERE `oxusername` = '".$this->_sDefaultUserName."'");

        if (!$sUserId) {
            $sUserName = $this->_sDefaultUserName;
            $sEncodedPassword = $this->_sNewEncodedPassword;
            $sSalt = $this->_sNewSalt;
            $sGermanyId = $this->_sAustriaId;

            /** @var User $oUser */
            $oUser = oxNew(User::class);
            $oUser->assign([
                'oxusername'  => $sUserName,
                'oxpassword'  => $sEncodedPassword,
                'oxpasssalt'  => $sSalt,
                'oxcountryid' => $sGermanyId,
                'oxrights'    => 'user',
                'active'      => true,
                'oxcompany'   => 'Your Company Name',
                'oxfname'     => 'John',
                'oxlname'     => 'Doe',
                'oxstreet'    => 'Maple Street',
                'oxstreetnr'  => '10',
                'oxcity'      => 'Any City',
                'oxzip'       => '9041',
                'oxfon'       => '217-8918712',
                'oxfax'       => '217-8918713',
                'oxsal'       => 'MR',
            ]);
            $oUser->save();

            $oObj = new BaseModel();
            $oObj->init('oxobject2group');
            $oObj->assign([
               'oxobjectid' => $oUser->getId(),
               'oxgroupsid' => 'oxidadmin',
            ]);
            $oObj->save();
        } else {
            $oUser = oxNew(User::class);
            $oUser->load($sUserId);
        }


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
