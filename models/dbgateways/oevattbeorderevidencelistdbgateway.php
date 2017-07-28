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
 * Order db gateway class
 */
class oeVATTBEOrderEvidenceListDbGateway extends oeVATTBEModelDbGateway
{
    /**
     * Save data to db.
     *
     * @param array $aData data
     *
     * @return bool
     */
    public function save($aData)
    {
        $oDb = $this->_getDb();

        $sSql = 'INSERT INTO `oevattbe_orderevidences` (oevattbe_orderid, oevattbe_evidence, oevattbe_countryid) VALUES ';
        $aSqlValues = array();

        $sOrderId = $aData['orderId'];
        $sOrderIdQuoted = $oDb->quote($sOrderId);
        foreach ($aData['evidenceList'] as $aEvidence) {
            $sNameQuoted = $oDb->quote($aEvidence['name']);
            $sCountryIdQuoted = $oDb->quote($aEvidence['countryId']);
            $aSqlValues[] = "($sOrderIdQuoted, $sNameQuoted, $sCountryIdQuoted)";
        }

        $blResult = true;
        if (!empty($aSqlValues)) {
            $sSql = $sSql . implode(', ', $aSqlValues) . ";";
            $blResult = $oDb->execute($sSql);
        }

        return $blResult;
    }

    /**
     * Load data from Db.
     *
     * @param string $sOrderId Order id.
     *
     * @return array
     */
    public function load($sOrderId)
    {
        $oDb = $this->_getDb();
        $sQuery = 'SELECT * FROM `oevattbe_orderevidences` WHERE `oevattbe_orderid` = ' . $oDb->quote($sOrderId);
        $aRecords = $oDb->getAll($sQuery);

        $aData = array();
        foreach ($aRecords as $aRecord) {
            $sName = $aRecord['OEVATTBE_EVIDENCE'];
            $aData[$sName]['name'] = $sName;
            $aData[$sName]['countryId'] = $aRecord['OEVATTBE_COUNTRYID'];
            $aData[$sName]['timestamp'] = $aRecord['OEVATTBE_TIMESTAMP'];
        }

        return $aData;
    }

    /**
     * Delete data from database.
     *
     * @param string $sOrderId Order id.
     *
     * @return bool
     */
    public function delete($sOrderId)
    {
        $oDb = $this->_getDb();
        $sQ = "delete from oevattbe_orderevidences where oevattbe_orderid = " . $oDb->quote($sOrderId);

        return $oDb->execute($sQ);
    }
}
