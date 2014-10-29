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
 * VAT TBE oxUser class
 */
class oeVATTBEOxUser extends oeVatTbeOxUser_parent
{
    /** @var oeVATTBETBEUser */
    private $_oTBEUser = null;

    /**
     * Returns users TBE country
     *
     * @return string
     */
    public function getOeVATTBETbeCountryId()
    {
        $oTBEUser = $this->_getTBEUser();
        return $oTBEUser->getOeVATTBETbeCountryId();
    }

    /**
     * Returns users TBE country
     *
     * @return string
     */
    public function getOeVATTBEEvidenceList()
    {
        $oTBEUser = $this->_getTBEUser();
        return $oTBEUser->getOeVATTBEEvidenceList();
    }

    /**
     * Returns users TBE country
     *
     * @return string
     */
    public function getOeVATTBETbeEvidenceUsed()
    {
        $oTBEUser = $this->_getTBEUser();
        return $oTBEUser->getOeVATTBETbeEvidenceUsed();
    }

    /**
     * Unset TBE country from caching to force recalculation on next get.
     * Wrapper for oeVATTBETBEUser::unsetTbeCountryFromCaching
     */
    public function unsetTbeCountryFromCaching()
    {
        $oTBEUser = $this->_getTBEUser();
        $oTBEUser->unsetTbeCountryFromCaching();
    }

    /**
     * Returns TBE User object.
     *
     * @return oeVATTBETBEUser
     */
    protected function _getTBEUser()
    {
        if (!$this->_oTBEUser) {
            $oSession = $this->getSession();
            $oConfig = $this->getConfig();

            $this->_oTBEUser = oxNew('oeVATTBETBEUser', $this, $oSession, $oConfig);
        }

        return $this->_oTBEUser;
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
        if ($this->getOeVATTBEVatIn() && $this->_isOeVATTBEINStoredDateEmpty()) {
            $this->oxuser__oevattbe_vatinenterdate = new oxField(date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime()));
        }
        $this->unsetTbeCountryFromCaching();

        return parent::save();
    }

    /**
     * VAT ID Store date
     *
     * @return string
     */
    public function getOeVATTBEVatInStoreDate()
    {
        return $this->oxuser__oevattbe_vatinenterdate->value;
    }

    /**
     * VAT ID
     *
     * @return string
     */
    public function getOeVATTBEVatIn()
    {
        return $this->oxuser__oxustid->value;
    }

    /**
     * Check if VAT in stored date is empty
     *
     * @return bool
     */
    protected function _isOeVATTBEINStoredDateEmpty()
    {
        return  is_null($this->getOeVATTBEVatInStoreDate()) || $this->getOeVATTBEVatInStoreDate() == '0000-00-00 00:00:00';
    }
}
