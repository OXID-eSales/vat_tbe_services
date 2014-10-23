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
 * VAT Groups db gateway class.
 */
class oeVATTBEArticleVATGroupsDbGateway extends oeVATTBEModelDbGateway
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

        $sArticleId = $aData['articleid'];

        $this->delete($sArticleId);

        $sSql = 'INSERT INTO `oevattbe_articlevat` (oevattbe_articleid, oevattbe_countryid, oevattbe_vatgroupid) VALUES ';
        $aSqlValues = array();

        $sArticleIdQuoted = $oDb->quote($sArticleId);
        foreach ($aData['relations'] as $sCountryId => $sGroupId) {
            $sCountryIdQuoted = $oDb->quote($sCountryId);
            $sGroupIdQuoted = $oDb->quote($sGroupId);
            $aSqlValues[] = "($sArticleIdQuoted, $sCountryIdQuoted, $sGroupIdQuoted)";
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
     * @param string $sArticleId VAT group id.
     *
     * @return array
     */
    public function load($sArticleId)
    {
        $oDb = $this->_getDb();
        $aData = $oDb->getAll('SELECT * FROM `oevattbe_articlevat` WHERE `oevattbe_articleid` = ' . $oDb->quote($sArticleId));

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

        $blDeleteResult = $oDb->execute('DELETE FROM `oevattbe_articlevat` WHERE `oevattbe_articleid` = ' . $oDb->quote($sGroupId));

        $blResult = ($blDeleteResult !== false);

        if ($blResult) {
            $oDb->commitTransaction();
        } else {
            $oDb->rollbackTransaction();
        }

        return $blResult;
    }
}
