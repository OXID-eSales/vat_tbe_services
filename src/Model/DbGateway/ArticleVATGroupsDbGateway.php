<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model\DbGateway;

use OxidEsales\EVatModule\Core\ModelDbGateway;

/**
 * VAT Groups db gateway class.
 */
class ArticleVATGroupsDbGateway extends ModelDbGateway
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

        $sArticleId = $aData['articleid'];

        $this->delete($sArticleId);

        $sSql = 'INSERT INTO `oevattbe_articlevat` (oevattbe_articleid, oevattbe_countryid, oevattbe_vatgroupid) VALUES ';
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
     * @param string $sArticleId VAT group id.
     *
     * @return array
     */
    public function load($sArticleId)
    {
        $oDb = $this->getDb();
        $aData = $oDb->getAll('SELECT * FROM `oevattbe_articlevat` WHERE `oevattbe_articleid` = ' . $oDb->quote($sArticleId));

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
        $aData = $oDb->getAll('SELECT * FROM `oevattbe_articlevat` WHERE `oevattbe_vatgroupid` = ' . $oDb->quote($sGroupId));

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
