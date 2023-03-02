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

namespace OxidEsales\EVatModule\Model\DbGateway;

use OxidEsales\EVatModule\Core\ModelDbGateway;

/**
 * VAT Groups db gateway class.
 */
class CategoryVATGroupsDbGateway extends ModelDbGateway
{
    /**
     * Saves VAT Group data to database.
     *
     * @param array $aData data
     *
     * @return bool
     */
    public function save($aData)
    {
        $oDb = $this->_getDb();

        $sCategoryId = $aData['categoryid'];

        $this->delete($sCategoryId);

        $sSql = 'INSERT INTO `oevattbe_categoryvat` (oevattbe_categoryid, oevattbe_countryid, oevattbe_vatgroupid) VALUES ';
        $aSqlValues = array();

        foreach ($aData['relations'] as $aValues) {
            $aQuoted = array_map(array($oDb, 'quote'), $aValues);
            $aSqlValues[] = "(".implode(',', $aQuoted).")";
        }

        if (!empty($aSqlValues)) {
            $sSql = $sSql . implode(', ', $aSqlValues) . ";";
            $oDb->execute($sSql);
        }

        return true;
    }

    /**
     * Load Article VAT Groups data from Db.
     *
     * @param string $sCategoryId VAT group id.
     *
     * @return array
     */
    public function load($sCategoryId)
    {
        $oDb = $this->_getDb();
        $aData = $oDb->getAll('SELECT * FROM `oevattbe_categoryvat` WHERE `oevattbe_categoryid` = ' . $oDb->quote($sCategoryId));

        return $aData;
    }

    /**
     * Load Article VAT Groups data from Db by group id.
     *
     * @param string $sGroupId Article id.
     *
     * @return array
     */
    public function loadByGroupId($sGroupId)
    {
        $oDb = $this->_getDb();
        $aData = $oDb->getAll('SELECT * FROM `oevattbe_categoryvat` WHERE `oevattbe_vatgroupid` = ' . $oDb->quote($sGroupId));

        return $aData;
    }

    /**
     * Delete VAT Group data from database.
     *
     * @param string $sGroupId VAT group id.
     *
     * @return bool
     */
    public function delete($sGroupId)
    {
        $oDb = $this->_getDb();
        $oDb->startTransaction();

        $blDeleteResult = $oDb->execute('DELETE FROM `oevattbe_categoryvat` WHERE `oevattbe_categoryid` = ' . $oDb->quote($sGroupId));

        $blResult = ($blDeleteResult !== false);

        if ($blResult) {
            $oDb->commitTransaction();
        } else {
            $oDb->rollbackTransaction();
        }

        return $blResult;
    }
}
