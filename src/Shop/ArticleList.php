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

namespace OxidEsales\EVatModule\Shop;

use OxidEsales\Eshop\Application\Model\Actions;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Core\Registry;
use \oxDb;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EVatModule\Model\ArticleSQLBuilder;

/**
 * VAT TBE oxArticle class
 */
class ArticleList extends ArticleList_parent
{
    /** @var ArticleSQLBuilder */
    private $_oVATTBEArticleSQLBuilder = null;

    /**
     * Creates SQL Statement to load Articles, etc.
     *
     * @param string $sFields        Fields which are loaded e.g. "oxid" or "*" etc.
     * @param string $sCatId         Category tree ID
     * @param array  $aSessionFilter Like array ( catid => array( attrid => value,...))
     *
     * @return string SQL
     */
    protected function getCategorySelect($sFields, $sCatId, $aSessionFilter)
    {
        if (!$this->isOeVATTBEConfigured()) {
            return parent::getCategorySelect($sFields, $sCatId, $aSessionFilter);
        }

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName('oxarticles');
        $sO2CView = $tableViewNameGenerator->getViewName('oxobject2category');

        // ----------------------------------
        // sorting
        $sSorting = '';
        if ($this->_sCustomSorting) {
            $sSorting = " {$this->_sCustomSorting} , ";
        }

        // ----------------------------------
        // filtering ?
        $sFilterSql = '';
        $iLang = Registry::getLang()->getBaseLanguage();
        if ($aSessionFilter && isset($aSessionFilter[$sCatId][$iLang])) {
            $sFilterSql = $this->getFilterSql($sCatId, $aSessionFilter[$sCatId][$iLang]);
        }

        $oDb = oxDb::getDb();

        $sSelect = "SELECT $sArticleTable.oxtimestamp, ";
        $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getSelectFields();
        $sSelect .= " FROM $sO2CView as oc";
        $sSelect .= " left join $sArticleTable ON $sArticleTable.oxid = oc.oxobjectid";
        $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getJoins();
        $sSelect .= " WHERE " . $this->getBaseObject()->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''";
        $sSelect .= " and oc.oxcatnid = " . $oDb->quote($sCatId) . " $sFilterSql ORDER BY $sSorting oc.oxpos, oc.oxobjectid ";

        return $sSelect;
    }

    /**
     * Builds vendor select SQL statement
     *
     * @param string $sVendorId Vendor ID
     *
     * @return string
     */
    protected function getVendorSelect($sVendorId)
    {
        if (!$this->isOeVATTBEConfigured()) {
            return parent::getVendorSelect($sVendorId);
        }

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName('oxarticles');
        $oBaseObject = $this->getBaseObject();

        $sSelect = "select ";
        $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getSelectFields();
        $sSelect .= " from $sArticleTable ";
        $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getJoins();
        $sSelect .= "where $sArticleTable.oxvendorid = " . oxDb::getDb()->quote($sVendorId) . " ";
        $sSelect .= " and " . $oBaseObject->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''  ";

        if ($this->_sCustomSorting) {
            $sSelect .= " ORDER BY {$this->_sCustomSorting} ";
        }

        return $sSelect;
    }

    /**
     * Builds Manufacturer select SQL statement
     *
     * @param string $sManufacturerId Manufacturer ID
     *
     * @return string
     */
    protected function getManufacturerSelect($sManufacturerId)
    {
        if (!$this->isOeVATTBEConfigured()) {
            return parent::getManufacturerSelect($sManufacturerId);
        }

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName('oxarticles');
        $oBaseObject = $this->getBaseObject();

        $sSelect = "select ";
        $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getSelectFields();
        $sSelect .= " from $sArticleTable ";
        $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getJoins();
        $sSelect .= "where $sArticleTable.oxmanufacturerid = " . oxDb::getDb()->quote($sManufacturerId) . " ";
        $sSelect .= " and " . $oBaseObject->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''  ";

        if ($this->_sCustomSorting) {
            $sSelect .= " ORDER BY {$this->_sCustomSorting} ";
        }

        return $sSelect;
    }

    /**
     * Builds SQL for selecting articles by price
     *
     * @param double $dPriceFrom Starting price
     * @param double $dPriceTo   Max price
     *
     * @return string
     */
    protected function getPriceSelect($dPriceFrom, $dPriceTo)
    {
        if (!$this->isOeVATTBEConfigured()) {
            return parent::getPriceSelect($dPriceFrom, $dPriceTo);
        }

        $oBaseObject = $this->getBaseObject();
        $sArticleTable = $oBaseObject->getViewName();

        $sSelect = "select ";
        $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getSelectFields();
        $sSelect .= " from $sArticleTable ";
        $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getJoins();
        $sSelect .= " where oxvarminprice >= 0 ";
        $sSelect .= $dPriceTo ? "and oxvarminprice <= " . (double) $dPriceTo . " " : " ";
        $sSelect .= $dPriceFrom ? "and oxvarminprice  >= " . (double) $dPriceFrom . " " : " ";

        $sSelect .= " and " . $oBaseObject->getSqlActiveSnippet() . " and {$sArticleTable}.oxissearch = 1";

        if (!$this->_sCustomSorting) {
            $sSelect .= " order by {$sArticleTable}.oxvarminprice asc , {$sArticleTable}.oxid";
        } else {
            $sSelect .= " order by {$this->_sCustomSorting}, {$sArticleTable}.oxid ";
        }

        return $sSelect;
    }

    /**
     * Loads shop AktionArticles.
     *
     * @param string $sActionID Action id
     * @param int    $iLimit    Select limit
     *
     * @return null
     */
    public function loadActionArticles($sActionID, $iLimit = null)
    {
        if (!$this->isOeVATTBEConfigured()) {
            parent::loadActionArticles($sActionID, $iLimit = null);
            return;
        }

        if (!trim($sActionID)) {
            return;
        }

        $sShopID = Registry::getConfig()->getShopId();
        $sActionID = oxDb::getDb()->quote(strtolower($sActionID));

        //echo $sSelect;
        $oBaseObject = $this->getBaseObject();
        $sArticleTable = $oBaseObject->getViewName();
        $sArticleFields = $oBaseObject->getSelectFields();

        $oBase = oxNew(Actions::class);
        $sActiveSql = $oBase->getSqlActiveSnippet();
        $sViewName = $oBase->getViewName();

        $sLimit = ($iLimit > 0) ? "limit " . $iLimit : '';

        $sSelect = "select ";
        $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getSelectFields();
        $sSelect .= " from oxactions2article";
        $sSelect .= " left join $sArticleTable on $sArticleTable.oxid = oxactions2article.oxartid";
        $sSelect .= " left join $sViewName on $sViewName.oxid = oxactions2article.oxactionid";
        $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getJoins();
        $sSelect .= " where oxactions2article.oxshopid = '$sShopID' and oxactions2article.oxactionid = $sActionID and $sActiveSql";
        $sSelect .= " and $sArticleTable.oxid is not null and " . $oBaseObject->getSqlActiveSnippet();
        $sSelect .= " order by oxactions2article.oxsort $sLimit";

        $this->selectString($sSelect);
    }

    /**
     * Loads article accessories
     *
     * @param string $sArticleId Article id
     *
     * @return null
     */
    public function loadArticleAccessoires($sArticleId)
    {
        if (!$this->isOeVATTBEConfigured()) {
            parent::loadArticleAccessoires($sArticleId);
            return;
        }
        $myConfig = Registry::getConfig();

        // Performance
        if (!$myConfig->getConfigParam('bl_perfLoadAccessoires')) {
            return;
        }

        $sArticleId = oxDb::getDb()->quote($sArticleId);

        $oBaseObject = $this->getBaseObject();
        $sArticleTable = $oBaseObject->getViewName();

        $sSelect = "select ";
        $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getSelectFields();
        $sSelect .= " from oxaccessoire2article ";
        $sSelect .= " left join $sArticleTable on oxaccessoire2article.oxobjectid=$sArticleTable.oxid ";
        $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getJoins();
        $sSelect .= "where oxaccessoire2article.oxarticlenid = $sArticleId ";
        $sSelect .= " and $sArticleTable.oxid is not null and " . $oBaseObject->getSqlActiveSnippet();
        //sorting articles
        $sSelect .= " order by oxaccessoire2article.oxsort";

        $this->selectString($sSelect);
    }

    /**
     * Loads article cross selling
     *
     * @param string $sArticleId Article id
     *
     * @return null
     */
    public function loadArticleCrossSell($sArticleId)
    {
        if (!$this->isOeVATTBEConfigured()) {
            parent::loadArticleCrossSell($sArticleId);
            return;
        }

        $myConfig = Registry::getConfig();

        // Performance
        if (!$myConfig->getConfigParam('bl_perfLoadCrossselling')) {
            return null;
        }

        $oBaseObject = $this->getBaseObject();
        $sArticleTable = $oBaseObject->getViewName();

        $sArticleId = oxDb::getDb()->quote($sArticleId);

        $sSelect = "SELECT ";
        $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getSelectFields();
        $sSelect .= " FROM $sArticleTable ";
        $sSelect .= " INNER JOIN oxobject2article ON oxobject2article.oxobjectid=$sArticleTable.oxid ";
        $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getJoins();
        $sSelect .= "WHERE oxobject2article.oxarticlenid = $sArticleId ";
        $sSelect .= " AND " . $oBaseObject->getSqlActiveSnippet();

        // #525 bidirectional cross selling
        if ($myConfig->getConfigParam('blBidirectCross')) {
            $sSelect = "
            (
                SELECT $sArticleTable.* FROM $sArticleTable
                    INNER JOIN oxobject2article AS O2A1 on
                        ( O2A1.oxobjectid = $sArticleTable.oxid AND O2A1.oxarticlenid = $sArticleId )
                WHERE 1
                AND " . $oBaseObject->getSqlActiveSnippet() . "
                AND ($sArticleTable.oxid != $sArticleId)
            )
            UNION
            (
                SELECT $sArticleTable.* FROM $sArticleTable
                    INNER JOIN oxobject2article AS O2A2 ON
                        ( O2A2.oxarticlenid = $sArticleTable.oxid AND O2A2.oxobjectid = $sArticleId )
                WHERE 1
                AND " . $oBaseObject->getSqlActiveSnippet() . "
                AND ($sArticleTable.oxid != $sArticleId)
            )";
        }

        $this->setSqlLimit(0, $myConfig->getConfigParam('iNrofCrossellArticles'));
        $this->selectString($sSelect);
    }

    /**
     * Loads newest shops articles from DB.
     *
     * @param int $iLimit Select limit
     *
     * @return null
     */
    public function loadNewestArticles($iLimit = null)
    {
        if (!$this->isOeVATTBEConfigured()) {
            parent::loadNewestArticles($iLimit);
            return;
        }
        $myConfig = Registry::getConfig();

        if (!$myConfig->getConfigParam('bl_perfLoadPriceForAddList')) {
            $this->getBaseObject()->disablePriceLoad();
        }

        $this->_aArray = array();
        switch ($myConfig->getConfigParam('iNewestArticlesMode')) {
            case 0:
                // switched off, do nothing
                break;
            case 1:
                // manually entered
                $this->loadActionArticles('oxnewest', $iLimit);
                break;
            case 2:
                $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
                $sArticleTable = $tableViewNameGenerator->getViewName('oxarticles');
                if ($myConfig->getConfigParam('blNewArtByInsert')) {
                    $sType = 'oxinsert';
                } else {
                    $sType = 'oxtimestamp';
                }
                $sSelect = "select ";
                $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getSelectFields();
                $sSelect .= " from $sArticleTable ";
                $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getJoins();
                $sSelect .= "where oxparentid = '' and " . $this->getBaseObject()->getSqlActiveSnippet() . " and oxissearch = 1 order by $sType desc ";
                if (!($iLimit = (int) $iLimit)) {
                    $iLimit = $myConfig->getConfigParam('iNrofNewcomerArticles');
                }
                $sSelect .= "limit " . $iLimit;

                $this->selectString($sSelect);

                break;
        }
    }

    /**
     * Load top 5 articles
     *
     * @param int $iLimit Select limit
     *
     * @return null
     */
    public function loadTop5Articles($iLimit = null)
    {
        if (!$this->isOeVATTBEConfigured()) {
            parent::loadTop5Articles($iLimit);
            return;
        }
        $myConfig = Registry::getConfig();

        if (!$myConfig->getConfigParam('bl_perfLoadPriceForAddList')) {
            $this->getBaseObject()->disablePriceLoad();
        }

        switch ($myConfig->getConfigParam('iTop5Mode')) {
            case 0:
                // switched off, do nothing
                break;
            case 1:
                // manually entered
                $this->loadActionArticles('oxtop5', $iLimit);
                break;
            case 2:
                $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
                $sArticleTable = $tableViewNameGenerator->getViewName('oxarticles');

                //by default limit 5
                $sLimit = ($iLimit > 0) ? "limit " . $iLimit : 'limit 5';

                $sSelect = "select ";
                $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getSelectFields();
                $sSelect .= " from $sArticleTable ";
                $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getJoins();
                $sSelect .= "where " . $this->getBaseObject()->getSqlActiveSnippet() . " and $sArticleTable.oxissearch = 1 ";
                $sSelect .= "and $sArticleTable.oxparentid = '' and $sArticleTable.oxsoldamount>0 ";
                $sSelect .= "order by $sArticleTable.oxsoldamount desc $sLimit";

                $this->selectString($sSelect);
                break;
        }
    }

    /**
     * Returns the appropriate SQL select
     *
     * @param string $sRecommendationId Recommlist ID
     * @param string $sArticlesFilter   Additional filter for recommlist's items
     *
     * @return string
     */
    protected function getArticleSelect($sRecommendationId, $sArticlesFilter = null)
    {
        if (!$this->isOeVATTBEConfigured()) {
            return parent::getArticleSelect($sRecommendationId, $sArticlesFilter);
        }

        $sRecommendationId = oxDb::getDb()->quote($sRecommendationId);

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName('oxarticles');

        $sSelect = "select distinct oxobject2list.oxdesc, ";
        $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getSelectFields();
        $sSelect .= " from oxobject2list ";
        $sSelect .= " left join $sArticleTable on oxobject2list.oxobjectid = $sArticleTable.oxid ";
        $sSelect .= $this->getOeVATTBEArticleSqlBuilder()->getJoins();
        $sSelect .= "where (oxobject2list.oxlistid = $sRecommendationId) " . $sArticlesFilter;

        return $sSelect;
    }

    /**
     * Returns users tbe country
     *
     * @return string
     */
    private function getOeVATTBETbeCountryId()
    {
        $sCountryId = null;
        $oUser = $this->getBaseObject()->getUser();

        if ($oUser) {
            $sCountryId = $oUser->getOeVATTBETbeCountryId();
        }

        return $sCountryId;
    }


    /**
     * Returns users tbe country
     *
     * @return string
     */
    private function isOeVATTBEConfigured()
    {
        $isConfigured = false;
        $sCountryId = $this->getOeVATTBETbeCountryId();
        if (!is_null($sCountryId)) {
            $oCountry = oxNew(Country::class);
            $oCountry->load($sCountryId);
            $isConfigured = $oCountry->appliesOeTBEVATTbeVat();
        }

        return $isConfigured;
    }

    /**
     * Article sql builder
     *
     * @return ArticleSQLBuilder
     */
    protected function getOeVATTBEArticleSqlBuilder()
    {
        if (is_null($this->_oVATTBEArticleSQLBuilder)) {
            $this->_oVATTBEArticleSQLBuilder = oxNew(ArticleSQLBuilder::class, $this->getBaseObject());
        }

        return $this->_oVATTBEArticleSQLBuilder;
    }
}
