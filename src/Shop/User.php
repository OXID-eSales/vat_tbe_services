<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Shop;

use OxidEsales\Eshop\Core\Exception\CookieException;
use OxidEsales\Eshop\Core\Exception\UserException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\User as EVatUser;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * VAT TBE oxUser class
 */
class User extends User_parent
{
    use ServiceContainer;

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
            $this->assign([
                'oevattbe_vatinenterdate' => date('Y-m-d H:i:s', Registry::getUtilsDate()->getTime())
            ]);
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
        return $this->getFieldData('oevattbe_vatinenterdate');
    }

    /**
     * VAT ID
     *
     * @return string
     */
    public function getOeVATTBEVatIn()
    {
        return $this->getFieldData('oxustid');
    }

    /**
     * Returns TBE User object.
     *
     * @return EVatUser
     */
    protected function getOeVATTBETBEUser()
    {
        if (!$this->_oTBEUser) {
            $this->_oTBEUser = $this->getServiceFromContainer(EVatUser::class);
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
