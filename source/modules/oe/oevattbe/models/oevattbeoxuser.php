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
 * VAT TBE oxUser class
 */
class oeVATTBEOxUser extends oeVatTbeOxUser_parent
{
    /**
     * Returns users TBE country
     *
     * @return string
     */
    public function getTbeCountryId()
    {
        $oSession = $this->getSession();
        $oConfig = $this->getConfig();

        /** @var oeVATTBETBEUser $oTBEUser */
        $oTBEUser = oxNew('oeVATTBETBEUser', $this, $oSession, $oConfig);

        return $oTBEUser->getTbeCountryId();
    }

    /**
     * Unset TBE country from caching to force recalculation on next get.
     * Wrapper for oeVATTBETBEUser::unsetTbeCountryFromCaching
     */
    public function unsetTbeCountryFromCaching()
    {
        $oSession = $this->getSession();
        $oConfig = $this->getConfig();

        /** @var oeVATTBETBEUser $oTBEUser */
        $oTBEUser = oxNew('oeVATTBETBEUser', $this, $oSession, $oConfig);
        $oTBEUser->unsetTbeCountryFromCaching();
    }

    /**
     * Checks if user country matches shop country.
     *
     * @return bool
     */
    public function isLocalUser()
    {
        $oSession = $this->getSession();
        $oConfig = $this->getConfig();

        /** @var oeVATTBETBEUser $oTBEUser */
        $oTBEUser = oxNew('oeVATTBETBEUser', $this, $oSession, $oConfig);

        return $oTBEUser->isLocalUser();
    }

    /**
     * Performs user login by username and password. Fetches user data from DB.
     * Registers in session. Returns true on success, FALSE otherwise.
     *
     * @param string $sUser     User username
     * @param string $sPassword User password
     * @param bool   $blCookie  (default false)
     *
     * @throws object
     * @throws oxCookieException
     * @throws oxUserException
     *
     * @return bool
     */
    public function login($sUser, $sPassword, $blCookie = false)
    {
        $this->unsetTbeCountryFromCaching();
        return parent::login($sUser, $sPassword, $blCookie);
    }

    /**
     * Logs out session user. Returns true on success
     *
     * @return bool
     */
    public function logout()
    {
        $this->unsetTbeCountryFromCaching();
        return parent::logout();
    }

    /**
     * Saves (updates) user object data information in DB. Return true on success.
     *
     * @return bool
     */
    public function save()
    {
        $this->unsetTbeCountryFromCaching();
        return parent::save();
    }
}
