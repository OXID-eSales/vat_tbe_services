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
 * VAT Groups db gateway class.
 */
class oeVATTBECountryVATGroupsDbGateway extends oeVATTBEModelDbGateway
{
    /**
     * Saves VAT Group data to database.
     *
     * @param array $aData
     *
     * @return bool
     */
    public function save($aData)
    {
        $oDb = $this->_getDb();

        foreach ($aData as $sField => $sData) {
            $aSql[] = '`' . $sField . '` = ' . $oDb->quote($sData);
        }

        $sSql = 'INSERT INTO `oevattbe_countryvatgroups` SET ';
        $sSql .= implode(', ', $aSql);
        $sSql .= ' ON DUPLICATE KEY UPDATE ';
        $sSql .= implode(', ', $aSql);
        $oDb->execute($sSql);

        $iGroupId = $aData['oevattbe_id'];
        if (empty($iGroupId)) {
            $iGroupId = $oDb->getOne('SELECT LAST_INSERT_ID()');
        }

        $oDb->execute('UPDATE `oxcountry` SET `oevattbe_istbevatconfigured` = 1 WHERE `oxid` = "'. $aData['oevattbe_countryid'] .'"');

        return $iGroupId;
    }

    /**
     * Load VAT Group data from Db for given country.
     *
     * @param string $sCountryId Country id. If no country id is given, all records will be returned.
     *
     * @return array
     */
    public function getList($sCountryId = null)
    {
        $oDb = $this->_getDb();
        $sQuery = 'SELECT * FROM `oevattbe_countryvatgroups`';
        if ($sCountryId) {
            $sQuery .= 'WHERE `oevattbe_countryid` = ' . $oDb->quote($sCountryId);
        }
        $sQuery .= ' ORDER BY `oevattbe_timestamp` DESC';

        return $oDb->getAll($sQuery);
    }

    /**
     * Load VAT Group data from Db.
     *
     * @param string $sGroupId VAT group id.
     *
     * @return array
     */
    public function load($sGroupId)
    {
        $oDb = $this->_getDb();
        $aData = $oDb->getRow('SELECT * FROM `oevattbe_countryvatgroups` WHERE `oevattbe_id` = ' . $oDb->quote($sGroupId));

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

        $aGroupInformation = $this->load($sGroupId);
        $sCountryId = $aGroupInformation['OEVATTBE_COUNTRYID'];

        $blDeleteResult = $oDb->execute('DELETE FROM `oevattbe_countryvatgroups` WHERE `oevattbe_id` = ' . $oDb->quote($sGroupId));
        $blResult = ($blDeleteResult !== false) ? true : false;
        $blDeleteResult = $oDb->execute('DELETE FROM `oevattbe_articlevat` WHERE `oevattbe_vatgroupid` = ' . $oDb->quote($sGroupId));
        $blResult = ($blDeleteResult !== false) ? $blResult : false;

        $bCountryHasGroup = (bool) $oDb->getOne(
            'SELECT `oevattbe_id`FROM `oevattbe_countryvatgroups` WHERE `oevattbe_countryid` = "'. $sCountryId .'" LIMIT 1'
        );

        if ($blResult) {
            if (!$bCountryHasGroup) {
                $oDb->execute('UPDATE `oxcountry`SET `oevattbe_istbevatconfigured` = 0 WHERE `oxid` = "'. $sCountryId .'"');
            }
            $oDb->commitTransaction();
        } else {
            $oDb->rollbackTransaction();
        }

        return $blResult;
    }
}
