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
    /** @var oeVATTBETBEUser */
    private $_oTBEUser = null;

    /**
     * Returns users TBE country
     *
     * @return string
     */
    public function getTbeCountryId()
    {
        $oTBEUser = $this->_getTBEUser();
        return $oTBEUser->getTbeCountryId();
    }

    /**
     * Returns users TBE country
     *
     * @return string
     */
    public function getTBEEvidenceList()
    {
        $oTBEUser = $this->_getTBEUser();
        return $oTBEUser->getTBEEvidenceList();
    }

    /**
     * Returns users TBE country
     *
     * @return string
     */
    public function getTbeEvidenceUsed()
    {
        $oTBEUser = $this->_getTBEUser();
        return $oTBEUser->getTbeEvidenceUsed();
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
        if ($this->getVatId() && !$this->getVatIdStoreDate()) {
            $this->oxuser__oevattbe_vatidenterdate = new oxField(date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime()));
        }

        $this->unsetTbeCountryFromCaching();
        return parent::save();
    }

    /**
     * VAT ID Store date
     *
     * @return string
     */
    public function getVatIdStoreDate()
    {
        return $this->oxuser__oevattbe_vatidenterdate->value;
    }

    /**
     * VAT ID
     *
     * @return string
     */
    public function getVatId()
    {
        return $this->oxuser__oxustId->value;
    }
}
