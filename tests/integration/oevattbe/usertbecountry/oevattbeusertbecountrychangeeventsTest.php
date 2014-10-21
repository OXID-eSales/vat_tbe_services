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
 * Testing TBEUser class.
 */
class Integration_oeVatTbe_userTBECountry_oeVATTBEUserTBECountryChangeEventsTest extends OxidTestCase
{

    /**
     * User created with billing country set as germany;
     * His TBE Country should also be germany.
     *
     * @return oxUser|oeVATTBEOxUser
     */
    public function testGetTbeCountryAfterUserCreated()
    {
        $sGermanyId = $this->_sGermanyId;
        $oUser = $this->_createUser();
        $this->assertSame($sGermanyId, $oUser->getTbeCountryId(), 'User created in Germany, so TBE country must be Germany.');

        return $oUser;
    }

    /**
     * User TBE Country is calculated;
     * User changes country;
     * User TBE Country should be recalculated.
     *
     * @param oxUser|oeVATTBEOxUser $oUser
     *
     * @depends testGetTbeCountryAfterUserCreated
     *
     * @return oxUser|oeVATTBEOxUser
     */
    public function testGetTbeCountryAfterUserChangeEvent($oUser)
    {
        $sAustriaId = $this->_sAustriaId;
        $oUser->oxuser__oxcountryid = new oxField($sAustriaId, oxField::T_RAW);

        $this->assertNotSame($sAustriaId, $oUser->getTbeCountryId());
        $oUser->save();
        $this->assertSame($sAustriaId, $oUser->getTbeCountryId());

        return $oUser;
    }

    /**
     * User TBE Country is calculated;
     * User logs out;
     * User TBE Country should be recalculated.
     *
     * @param oxUser|oeVATTBEOxUser $oUser
     *
     * @depends testGetTbeCountryAfterUserChangeEvent
     *
     * @return oxUser|oeVATTBEOxUser
     */
    public function testGetTbeCountryAfterLogout($oUser)
    {
        $sUnitedKingdom = $this->_sUnitedKingdom;
        $oUser->oxuser__oxcountryid = new oxField($sUnitedKingdom, oxField::T_RAW);

        $this->assertNotSame($sUnitedKingdom, $oUser->getTbeCountryId());
        $oUser->logout();
        $this->assertSame($sUnitedKingdom, $oUser->getTbeCountryId());

        return $oUser;
    }

    /**
     * User TBE Country is calculated;
     * User fails to log in (wrong password);
     * User TBE Country should not be recalculated.
     *
     * @param oxUser|oeVATTBEOxUser $oUser
     *
     * @depends testGetTbeCountryAfterLogout
     *
     * @return oxUser|oeVATTBEOxUser
     */
    public function testGetTbeCountryAfterUserFailsLogIn($oUser)
    {
        $sAustriaId = $this->_sAustriaId;
        $sUserName = $this->_sDefaultUserName;
        $sWrongUserPassword = 'wrong password';

        $this->assertNotSame($sAustriaId, $oUser->getTbeCountryId());
        $blLogsIn = $oUser->login($sUserName, $sWrongUserPassword);
        $this->assertTrue($blLogsIn, 'User did not log in successfully.');
        $this->assertNotSame($sAustriaId, $oUser->getTbeCountryId());

        return $oUser;
    }

    /**
     * User TBE Country is calculated;
     * User logs in;
     * User TBE Country should be recalculated.
     *
     * @param oxUser|oeVATTBEOxUser $oUser
     *
     * @depends testGetTbeCountryAfterUserFailsLogIn
     */
    public function testGetTbeCountryAfterUserLogsIn($oUser)
    {
        $sAustriaId = $this->_sAustriaId;
        $sUserName = $this->_sDefaultUserName;
        $sUserPassword = $this->_sDefaultUserPassword;

        $this->assertNotSame($sAustriaId, $oUser->getTbeCountryId());
        $blLogsIn = $oUser->login($sUserName, $sUserPassword);
        $this->assertTrue($blLogsIn, 'User did not log in successfully.');
        $this->assertSame($sAustriaId, $oUser->getTbeCountryId());
    }

    /**
     * Creates used object for use in tests.
     *
     * @return oxUser|oeVATTBEOxUser
     */
    private function _createUser()
    {
        $sUserName = $this->_sDefaultUserName;
        $sEncodedPassword = $this->_sNewEncodedPassword;
        $sSalt = $this->_sNewSalt;
        $sGermanyId = $this->_sGermanyId;

        $oUser = oxNew('oeVATTBEOxUser');
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

    /** @var string Germany ID. */
    private $_sGermanyId = '56784f8ffc657fff84915b93e12a626e';

    /** @var string Austria ID. */
    private $_sAustriaId = 'a7c40f6320aeb2ec2.72885259';

    /** @var string United Kingdom ID. */
    private $_sUnitedKingdom = 'a7c40f632a0804ab5.18804076';
}
