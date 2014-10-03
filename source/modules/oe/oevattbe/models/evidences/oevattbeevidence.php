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
 * @copyright (C) OXID eSales AG 2003-2014T
 */


/**
 * Class checks all collected evidences and provides user country from them.
 */
abstract class oeVATTBEEvidence
{
    /** @var oxUser */
    private $_oUser = null;

    /**
     * Sets required dependencies.
     *
     * @param oxUser $oUser
     */
    public function __construct($oUser)
    {
        $this->_oUser = $oUser;
    }

    /**
     * Returns evidence name.
     *
     * @return string Evidence name.
     */
    abstract public function getName();

    /**
     * Gets user country id and returns it.
     * Has local cache, so does not recheck twice.
     *
     * @return string Country id.
     */
    abstract public function getCountryId();

    /**
     * Returns oxUser object.
     *
     * @return oxUser
     */
    protected function _getUser()
    {
        return $this->_oUser;
    }
}
