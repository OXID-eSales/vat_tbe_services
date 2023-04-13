<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model\DbGateway;

use OxidEsales\EVatModule\Core\ModelDbGateway;
use OxidEsales\EVatModule\Core\ModelDbGatewayInterface;

/**
 * VAT Groups db gateway class.
 */
class CategoryVATGroupsDbGateway extends ModelDbGateway implements ModelDbGatewayInterface
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
        $oDb = $this->getDb();

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
        $oDb = $this->getDb();
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
        $oDb = $this->getDb();
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
        $oDb = $this->getDb();
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
