<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Country;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;
use PHPUnit\Framework\Attributes\ExcludeGlobalVariableFromBackup;

/**
 * Testing TBEUser class.
 */
class CountryChangeEventsTest extends BaseTestCase
{
    /** @var string */
    protected $_sDefaultUserName = '_testUserName@oxid-esales.com';

    /** @var string */
    protected $_sDefaultUserPassword = '_testPassword';

    /** @var string encoded default password */
    private $_sNewEncodedPassword = 'b016e37ac8ec71449b475e84a941e3c39a27fb8f0710d4b47d6116ad6a6afcaa0c17006a4c01ffc67f3db95772fe001584cb4ce7e5bacd74198c24d1851841d5';

    /** @var string Salt generated with new algorithm. */
    private $_sNewSalt = '56784f8ffc657fff84915b93e12a626e';

    /** @var string Germany ID. */
    private $_sGermanyId = 'a7c40f631fc920687.20179984';

    /** @var string Austria ID. */
    private $_sAustriaId = 'a7c40f6320aeb2ec2.72885259';

    /** @var string United Kingdom ID. */
    private $_sUnitedKingdom = 'a7c40f632a0804ab5.18804076';

    /**
     * User created with billing country set as germany;
     * His TBE Country should also be germany.
     *
     * @return User
     */
    #[ExcludeGlobalVariableFromBackup("_SESSION")]
    public function testGetOeVATTBECountryAfterUserCreated()
    {
        $sGermanyId = $this->_sGermanyId;
        $oUser = $this->_createUser();
        Registry::getSession()->setUser($oUser);

        $this->assertSame($sGermanyId, $oUser->getOeVATTBETbeCountryId(), 'User created in Germany, so TBE country must be Germany.');

        return $oUser;
    }

    /**
     * User TBE Country is calculated;
     * User changes country;
     * User TBE Country should be recalculated.
     *
     * @param User $oUser
     *
     * @depends testGetOeVATTBECountryAfterUserCreated
     *
     * @return User
     */
    public function testGetOeVATTBECountryAfterUserChangeEvent($oUser)
    {
        $sAustriaId = $this->_sAustriaId;
        $oUser->assign([
            'oxcountryid' => $sAustriaId
        ]);

        $this->assertNotSame($sAustriaId, $oUser->getOeVATTBETbeCountryId());
        $oUser->save();

        $this->assertSame($sAustriaId, $oUser->getOeVATTBETbeCountryId());

        return $oUser;
    }

    /**
     * User TBE Country is calculated;
     * User logs out;
     * User TBE Country should be recalculated.
     *
     * @param User $oUser
     *
     * @depends testGetOeVATTBECountryAfterUserChangeEvent
     *
     * @return User
     */
    public function testGetOeVATTBECountryAfterLogout($oUser)
    {
        $sUnitedKingdom = $this->_sUnitedKingdom;
        $oUser->assign([
            'oxcountryid' => $sUnitedKingdom
        ]);

        $this->assertNotSame($sUnitedKingdom, $oUser->getOeVATTBETbeCountryId());
        $oUser->logout();
        Registry::getSession()->setUser($oUser);
        $this->assertSame($sUnitedKingdom, $oUser->getOeVATTBETbeCountryId());

        return $oUser;
    }

    /**
     * User TBE Country is calculated;
     * User fails to log in (wrong password);
     * User TBE Country should not be recalculated.
     *
     * @param User $oUser
     *
     * @depends testGetOeVATTBECountryAfterLogout
     *
     * @return User
     */
    public function testGetOeVATTBECountryAfterUserFailsLogIn($oUser)
    {
        $sAustriaId = $this->_sAustriaId;
        $sUserName = $this->_sDefaultUserName;
        $sWrongUserPassword = 'wrong password';

        $this->assertNotSame($sAustriaId, $oUser->getOeVATTBETbeCountryId());

        try {
            $oUser->login($sUserName, $sWrongUserPassword);
            $this->fail('expected to see an exception');
        } catch (\OxidEsales\Eshop\Core\Exception\UserException $exception) {
            $this->assertEquals('ERROR_MESSAGE_USER_NOVALIDLOGIN', $exception->getMessage());
        }

        $this->assertNotSame($sAustriaId, $oUser->getOeVATTBETbeCountryId());

        return $oUser;
    }

    /**
     * User TBE Country is calculated;
     * User logs in;
     * User TBE Country should be recalculated.
     *
     * @param User $oUser
     *
     * @depends testGetOeVATTBECountryAfterUserFailsLogIn
     */
    public function testGetOeVATTBECountryAfterUserLogsIn($oUser)
    {
        $sAustriaId = $this->_sAustriaId;
        $sUserName = $this->_sDefaultUserName;
        $sUserPassword = $this->_sDefaultUserPassword;

        $this->assertNotSame($sAustriaId, $oUser->getOeVATTBETbeCountryId());
        $blLogsIn = $oUser->login($sUserName, $sUserPassword);
        $this->assertTrue($blLogsIn, 'User did not log in successfully.');
        $this->assertSame($sAustriaId, $oUser->getOeVATTBETbeCountryId());
    }

    /**
     * Creates used object for use in tests.
     *
     * @return User
     */
    private function _createUser()
    {
        $oUser = oxNew(User::class);
        $oUser->assign([
            'oxusername'  => $this->_sDefaultUserName,
            'oxpassword'  => $this->_sNewEncodedPassword,
            'oxpasssalt'  => $this->_sNewSalt,
            'oxcountryid' => $this->_sGermanyId,
        ]);
        $oUser->save();

        return $oUser;
    }
}
