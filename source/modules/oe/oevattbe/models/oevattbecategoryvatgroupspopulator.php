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
 * VAT Groups handling class
 */
class oeVATTBECategoryVATGroupsPopulator
{

    /**
     * Handles class dependencies.
     *
     * @param oeVATTBECategoryVATGroupsPopulatorDbGateway $oDbGateway db gateway
     */
    public function __construct($oDbGateway)
    {
        $this->_oDbGateway = $oDbGateway;
    }

    /**
     * Creates an instance of oeVATTBECategoryVATGroupsPopulator.
     *
     * @return oeVATTBECategoryVATGroupsPopulator;
     */
    public static function createInstance()
    {
        $oGateway = oxNew('oeVATTBECategoryVATGroupsPopulatorDbGateway');
        $oList = oxNew('oeVATTBECategoryVATGroupsPopulator', $oGateway);

        return $oList;
    }

    /**
     * Method for model saving (insert and update data).
     *
     * @param oxCategory|oeVATTBEOxCategory $oCategory category
     */
    public function populate($oCategory)
    {
        $this->_getDbGateway()->populate($oCategory->getId());
    }

    /**
     * Returns model database gateway.
     *
     * @return oeVATTBECategoryVATGroupsPopulatorDbGateway
     */
    protected function _getDbGateway()
    {
        return $this->_oDbGateway;
    }
}
