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

namespace OxidEsales\EVatModule\Shop;

use OxidEsales\Eshop\Core\Exception\CookieException;
use OxidEsales\Eshop\Core\Exception\UserException;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\User as EVatUser;

/**
 * VAT TBE oxUser class
 */
class User extends User_parent
{
    /** @var EVatUser */
    private $_oTBEUser = null;

    /**
     * Performs user login by username and password. Fetches user data from DB.
     * Registers in session. Returns true on success, FALSE otherwise.
     *
     * @param string $sUser     User username
     * @param string $sPassword User password
     * @param bool   $blCookie  (default false)
     *
     * @throws object
     * @throws CookieException
     * @throws UserException
     *
     * @return bool
     */
    public function login($sUser, $sPassword, $blCookie = false)
    {
        $this->unsetOeVATTBETbeCountryFromCaching();
        return parent::login($sUser, $sPassword, $blCookie);
    }

    /**
     * Logs out session user. Returns true on success
     *
     * @return bool
     */
    public function logout()
    {
        $this->unsetOeVATTBETbeCountryFromCaching();
        return parent::logout();
    }

    /**
     * Saves (updates) user object data information in DB. Return true on success.
     *
     * @return bool
     */
    public function save()
    {
        if ($this->getOeVATTBEVatIn() && $this->isOeVATTBEINStoredDateEmpty()) {
            $this->oxuser__oevattbe_vatinenterdate = new Field(date('Y-m-d H:i:s', Registry::get("oxUtilsDate")->getTime()));
        }
        $this->unsetOeVATTBETbeCountryFromCaching();

        return parent::save();
    }

    /**
     * Returns users TBE country
     *
     * @return string
     */
    public function getOeVATTBETbeCountryId()
    {
        $oTBEUser = $this->getOeVATTBETBEUser();
        return $oTBEUser->getOeVATTBETbeCountryId();
    }

    /**
     * Returns users TBE country
     *
     * @return string
     */
    public function getOeVATTBEEvidenceList()
    {
        $oTBEUser = $this->getOeVATTBETBEUser();
        return $oTBEUser->getOeVATTBEEvidenceList();
    }

    /**
     * Returns users TBE country
     *
     * @return string
     */
    public function getOeVATTBETbeEvidenceUsed()
    {
        $oTBEUser = $this->getOeVATTBETBEUser();
        return $oTBEUser->getOeVATTBETbeEvidenceUsed();
    }

    /**
     * Unset TBE country from caching to force recalculation on next get.
     * Wrapper for oeVATTBETBEUser::unsetOeVATTBETbeCountryFromCaching
     */
    public function unsetOeVATTBETbeCountryFromCaching()
    {
        $oTBEUser = $this->getOeVATTBETBEUser();
        $oTBEUser->unsetOeVATTBETbeCountryFromCaching();
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
     * Returns TBE User object.
     *
     * @return EVatUser
     */
    protected function getOeVATTBETBEUser()
    {
        if (!$this->_oTBEUser) {
            $oSession = Registry::getSession();
            $oConfig = Registry::getConfig();

            $this->_oTBEUser = oxNew(EVatUser::class, $this, $oSession, $oConfig);
        }

        return $this->_oTBEUser;
    }

    /**
     * Check if VAT in stored date is empty
     *
     * @return bool
     */
    protected function isOeVATTBEINStoredDateEmpty()
    {
        return  is_null($this->getOeVATTBEVatInStoreDate()) || $this->getOeVATTBEVatInStoreDate() == '0000-00-00 00:00:00';
    }
}
