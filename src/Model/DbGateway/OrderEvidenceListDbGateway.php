<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model\DbGateway;

use OxidEsales\EVatModule\Core\ModelDbGateway;
use OxidEsales\EVatModule\Core\ModelDbGatewayInterface;

/**
 * Order db gateway class
 */
class OrderEvidenceListDbGateway extends ModelDbGateway implements ModelDbGatewayInterface
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
        $oDb = $this->getDb();

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
        $oDb = $this->getDb();
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
        $oDb = $this->getDb();
        $sQ = "delete from oevattbe_orderevidences where oevattbe_orderid = " . $oDb->quote($sOrderId);

        return $oDb->execute($sQ);
    }
}
