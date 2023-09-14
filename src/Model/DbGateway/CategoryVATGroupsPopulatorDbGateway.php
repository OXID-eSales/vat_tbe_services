<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model\DbGateway;

use \oxDb;
use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;

/**
 * Category VAT Groups populator db gateway class.
 */
class CategoryVATGroupsPopulatorDbGateway
{
    /**
     * Update category article with the same information
     *
     * @param string $sCategoryId category id
     */
    public function populate($sCategoryId)
    {
        $this->deleteArticlesGroups($sCategoryId);
        $this->setArticlesGroups($sCategoryId);
        $this->setArticlesAsTBEServices($sCategoryId);
    }

    /**
     * Resets articles to be not TBE services.
     *
     * @param array $aArticleIds
     *
     * @return bool
     */
    public function reset($aArticleIds)
    {
        $blResult = false;
        if ($aArticleIds) {
            $oDb = oxNew(oxDb::class)->getDb();
            $sArticleIds = implode(', ', $oDb->quoteArray($aArticleIds));
            $blResult = $this->makeArticlesNotTBE($sArticleIds) && $this->removeFromVATGroups($sArticleIds);
        }


        return $blResult;
    }

    /**
     * Delete category articles VAT Group data from database.
     *
     * @param string $sCategoryId category id.
     *
     * @return bool
     */
    protected function deleteArticlesGroups($sCategoryId)
    {
        $oDb = $this->getDb();

        $sSql = '
          DELETE `oevattbe_articlevat`.*
          FROM `oevattbe_articlevat`
          INNER JOIN `oxobject2category` ON `oxobject2category`.`oxobjectid` = `oevattbe_articlevat`.`oevattbe_articleid`
          WHERE `oxobject2category`.`oxcatnid` = ' . $oDb->quote($sCategoryId);

        return $oDb->execute($sSql);
    }

    /**
     * Populates category VAT Group info to articles
     *
     * @param string $sCategoryId categoryId
     *
     * @return bool
     */
    protected function setArticlesGroups($sCategoryId)
    {
        $oDb = $this->getDb();

        $sSql = 'INSERT INTO `oevattbe_articlevat` (`oevattbe_articleid`, `oevattbe_countryid`, `oevattbe_vatgroupid`)
              SELECT DISTINCT `oxobject2category`.`oxobjectid`, `oevattbe_categoryvat`.`oevattbe_countryid`, `oevattbe_categoryvat`.`oevattbe_vatgroupid`
              FROM `oxobject2category`
              LEFT JOIN `oevattbe_categoryvat` ON `oxobject2category`.`oxcatnid` = `oevattbe_categoryvat`.`oevattbe_categoryid`
              WHERE `oevattbe_categoryvat`.`oevattbe_categoryid` = '. $oDb->quote($sCategoryId);

        return $oDb->execute($sSql);
    }

    /**
     * Populates category tbe service info to articles
     *
     * @param string $sCategoryId categoryId
     *
     * @return bool
     */
    protected function setArticlesAsTBEServices($sCategoryId)
    {
        $oDb = $this->getDb();

        $sSql = 'UPDATE `oxarticles`
              INNER JOIN `oxobject2category` ON `oxobject2category`.`oxobjectid` = `oxarticles`.`oxid`
              LEFT JOIN `oxcategories` ON `oxobject2category`.`oxcatnid` = `oxcategories`.`oxid`
              SET  `oxarticles`.`oevattbe_istbeservice` = `oxcategories`.`oevattbe_istbe`
              WHERE `oxobject2category`.`oxcatnid` = '. $oDb->quote($sCategoryId);

        return $oDb->execute($sSql);
    }

    /**
     * Returns data base resource.
     *
     * @return DatabaseInterface
     */
    protected function getDb()
    {
        return oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
    }

    /**
     * Removes TBE flag from articles.
     *
     * @param string $sArticleIds
     *
     * @return array
     */
    protected function makeArticlesNotTBE($sArticleIds)
    {
        $sSqlToUpdateArticles = '
              UPDATE `oxarticles`
              SET  `oxarticles`.`oevattbe_istbeservice` = 0
              WHERE `oxarticles`.`oxid`
              IN (' . $sArticleIds . ')';

        return $this->getDb()->execute($sSqlToUpdateArticles);
    }

    /**
     * Removes from articles VAT groups.
     *
     * @param string $sArticleIds
     *
     * @return bool
     */
    protected function removeFromVATGroups($sArticleIds)
    {
        $sSqlToRemoveRates = '
              DELETE FROM `oevattbe_articlevat`
              WHERE `oevattbe_articlevat`.`oevattbe_articleid`
              IN (' . $sArticleIds . ')';

        return $this->getDb()->execute($sSqlToRemoveRates);
    }
}
