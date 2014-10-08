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
 * VAT TBE oxArticle class
 */
class oeVATTBEOxArticleList extends oeVATTBEOxArticleList_parent
{
    /**
     * Creates SQL Statement to load Articles, etc.
     *
     * @param string $sFields        Fields which are loaded e.g. "oxid" or "*" etc.
     * @param string $sCatId         Category tree ID
     * @param array  $aSessionFilter Like array ( catid => array( attrid => value,...))
     *
     * @return string SQL
     */
    protected function _getCategorySelect($sFields, $sCatId, $aSessionFilter)
    {
        if (!is_null($this->_getTbeCountryId())) {

            $sArticleTable = getViewName('oxarticles');
            $sO2CView = getViewName('oxobject2category');

            // ----------------------------------
            // sorting
            $sSorting = '';
            if ($this->_sCustomSorting) {
                $sSorting = " {$this->_sCustomSorting} , ";
            }

            // ----------------------------------
            // filtering ?
            $sFilterSql = '';
            $iLang = oxRegistry::getLang()->getBaseLanguage();
            if ($aSessionFilter && isset($aSessionFilter[$sCatId][$iLang])) {
                $sFilterSql = $this->_getFilterSql($sCatId, $aSessionFilter[$sCatId][$iLang]);
            }

            $oDb = oxDb::getDb();

            $sSelect = "SELECT $sFields, $sArticleTable.oxtimestamp";
            $sSelect .= " , `oevattbe_countryvatgroups`.`oevattbe_rate` ";
            $sSelect .= " FROM $sO2CView as oc";
            $sSelect .= " left join $sArticleTable ON $sArticleTable.oxid = oc.oxobjectid";
            $sSelect .= " LEFT JOIN `oevattbe_articlevat` ON `" . $sArticleTable . "`.`oxid` = `oevattbe_articlevat`.`oevattbe_articleid` ";
            $sSelect .= "       AND `oevattbe_articlevat`.`oevattbe_countryid` = " . oxDb::getDb()->quote($this->_getTbeCountryId());
            $sSelect .= " LEFT JOIN `oevattbe_countryvatgroups` ON `oevattbe_articlevat`.`oevattbe_vatgroupid` = `oevattbe_countryvatgroups`.`oevattbe_id` ";
            $sSelect .= " WHERE " . $this->getBaseObject()->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''";
            $sSelect .= " and oc.oxcatnid = " . $oDb->quote($sCatId) . " $sFilterSql ORDER BY $sSorting oc.oxpos, oc.oxobjectid ";

        } else {
            $sSelect = parent::_getCategorySelect($sFields, $sCatId, $aSessionFilter);
        }



        return $sSelect;
    }

    /**
     * Builds vendor select SQL statement
     *
     * @param string $sVendorId Vendor ID
     *
     * @return string
     */
    protected function _getVendorSelect($sVendorId)
    {
        if (!is_null($this->_getTbeCountryId())) {
            $sArticleTable = getViewName('oxarticles');
            $oBaseObject = $this->getBaseObject();
            $sFieldNames = $oBaseObject->getSelectFields();
            $sSelect = "select $sFieldNames ";
            $sSelect .= " , `oevattbe_countryvatgroups`.`oevattbe_rate` ";
            $sSelect .= " from $sArticleTable ";
            $sSelect .= " LEFT JOIN `oevattbe_articlevat` ON `" . $sArticleTable . "`.`oxid` = `oevattbe_articlevat`.`oevattbe_articleid` ";
            $sSelect .= "       AND `oevattbe_articlevat`.`oevattbe_countryid` = " . oxDb::getDb()->quote($this->_getTbeCountryId());
            $sSelect .= " LEFT JOIN `oevattbe_countryvatgroups` ON `oevattbe_articlevat`.`oevattbe_vatgroupid` = `oevattbe_countryvatgroups`.`oevattbe_id` ";
            $sSelect .= "where $sArticleTable.oxvendorid = " . oxDb::getDb()->quote($sVendorId) . " ";
            $sSelect .= " and " . $oBaseObject->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''  ";

            if ($this->_sCustomSorting) {
                $sSelect .= " ORDER BY {$this->_sCustomSorting} ";
            }
        } else {
            $sSelect = parent::_getVendorSelect($sVendorId);
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
    protected function _getManufacturerSelect($sManufacturerId)
    {
        if (!is_null($this->_getTbeCountryId())) {
            $sArticleTable = getViewName('oxarticles');
            $oBaseObject = $this->getBaseObject();
            $sFieldNames = $oBaseObject->getSelectFields();
            $sSelect = "select $sFieldNames ";
            $sSelect .= " , `oevattbe_countryvatgroups`.`oevattbe_rate` ";
            $sSelect .= " from $sArticleTable ";
            $sSelect .= " LEFT JOIN `oevattbe_articlevat` ON `" . $sArticleTable . "`.`oxid` = `oevattbe_articlevat`.`oevattbe_articleid` ";
            $sSelect .= "       AND `oevattbe_articlevat`.`oevattbe_countryid` = " . oxDb::getDb()->quote($this->_getTbeCountryId());
            $sSelect .= " LEFT JOIN `oevattbe_countryvatgroups` ON `oevattbe_articlevat`.`oevattbe_vatgroupid` = `oevattbe_countryvatgroups`.`oevattbe_id` ";
            $sSelect .= "where $sArticleTable.oxmanufacturerid = " . oxDb::getDb()->quote($sManufacturerId) . " ";
            $sSelect .= " and " . $oBaseObject->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''  ";

            if ($this->_sCustomSorting) {
                $sSelect .= " ORDER BY {$this->_sCustomSorting} ";
            }
        } else {
            $sSelect = parent::_getManufacturerSelect($sManufacturerId);
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
    protected function _getPriceSelect($dPriceFrom, $dPriceTo)
    {
        if (!is_null($this->_getTbeCountryId())) {
            $oBaseObject = $this->getBaseObject();
            $sArticleTable = $oBaseObject->getViewName();
            $sSelectFields = $oBaseObject->getSelectFields();

            $sSelect = "select $sSelectFields ";
            $sSelect .= " , `oevattbe_countryvatgroups`.`oevattbe_rate` ";
            $sSelect .= " from $sArticleTable ";
            $sSelect .= " LEFT JOIN `oevattbe_articlevat` ON `" . $sArticleTable . "`.`oxid` = `oevattbe_articlevat`.`oevattbe_articleid` ";
            $sSelect .= "       AND `oevattbe_articlevat`.`oevattbe_countryid` = " . oxDb::getDb()->quote($this->_getTbeCountryId());
            $sSelect .= " LEFT JOIN `oevattbe_countryvatgroups` ON `oevattbe_articlevat`.`oevattbe_vatgroupid` = `oevattbe_countryvatgroups`.`oevattbe_id` ";
            $sSelect .= " where oxvarminprice >= 0 ";
            $sSelect .= $dPriceTo ? "and oxvarminprice <= " . (double) $dPriceTo . " " : " ";
            $sSelect .= $dPriceFrom ? "and oxvarminprice  >= " . (double) $dPriceFrom . " " : " ";

            $sSelect .= " and " . $oBaseObject->getSqlActiveSnippet() . " and {$sArticleTable}.oxissearch = 1";

            if (!$this->_sCustomSorting) {
                $sSelect .= " order by {$sArticleTable}.oxvarminprice asc , {$sArticleTable}.oxid";
            } else {
                $sSelect .= " order by {$this->_sCustomSorting}, {$sArticleTable}.oxid ";
            }
        } else {
            $sSelect = parent::_getPriceSelect($dPriceFrom, $dPriceTo);
        }

        return $sSelect;
    }

    /**
     * Loads a list of articles having
     *
     * @param string $sTag  Searched tag
     * @param int    $iLang Active language
     *
     * @return int
     */
    public function loadTagArticles($sTag, $iLang)
    {
        if (!is_null($this->_getTbeCountryId())) {
            $oListObject = $this->getBaseObject();
            $sArticleTable = $oListObject->getViewName();
            $sArticleFields = $oListObject->getSelectFields();
            $sViewName = getViewName('oxartextends', $iLang);

            $oTag = oxNew('oxtag', $sTag);
            $oTag->addUnderscores();
            $sTag = $oTag->get();

            $sQ = "select {$sArticleFields}";
            $sQ .= " , `oevattbe_countryvatgroups`.`oevattbe_rate` ";
            $sQ .= " from {$sViewName} ";
            $sQ .= " inner join {$sArticleTable} on {$sArticleTable}.oxid = {$sViewName}.oxid ";
            $sQ .= " LEFT JOIN `oevattbe_articlevat` ON `" . $sArticleTable . "`.`oxid` = `oevattbe_articlevat`.`oevattbe_articleid` ";
            $sQ .= "       AND `oevattbe_articlevat`.`oevattbe_countryid` = " . oxDb::getDb()->quote($this->_getTbeCountryId());
            $sQ .= " LEFT JOIN `oevattbe_countryvatgroups` ON `oevattbe_articlevat`.`oevattbe_vatgroupid` = `oevattbe_countryvatgroups`.`oevattbe_id` ";
            $sQ .= " where {$sArticleTable}.oxparentid = '' AND match ( {$sViewName}.oxtags ) ";
            $sQ .= " against( " . oxDb::getDb()->quote("\"" . $sTag . "\"") . " IN BOOLEAN MODE )";

            // checking stock etc
            if (($sActiveSnippet = $oListObject->getSqlActiveSnippet())) {
                $sQ .= " and {$sActiveSnippet}";
            }

            if ($this->_sCustomSorting) {
                $sSort = $this->_sCustomSorting;
                if (strpos($sSort, '.') === false) {
                    $sSort = $sArticleTable . '.' . $sSort;
                }
                $sQ .= " order by $sSort ";
            }

            $this->selectString($sQ);

            // calc count - we can not use count($this) here as we might have paging enabled
            return oxRegistry::get("oxUtilsCount")->getTagArticleCount($sTag, $iLang);
        } else {
            return parent::loadTagArticles($sTag, $iLang);
        }

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
        if (!is_null($this->_getTbeCountryId())) {
            // Performance
            if (!trim($sActionID)) {
                return;
            }

            $sShopID = $this->getConfig()->getShopId();
            $sActionID = oxDb::getDb()->quote(strtolower($sActionID));

            //echo $sSelect;
            $oBaseObject = $this->getBaseObject();
            $sArticleTable = $oBaseObject->getViewName();
            $sArticleFields = $oBaseObject->getSelectFields();

            $oBase = oxNew("oxactions");
            $sActiveSql = $oBase->getSqlActiveSnippet();
            $sViewName = $oBase->getViewName();

            $sLimit = ($iLimit > 0) ? "limit " . $iLimit : '';

            $sSelect = "select $sArticleFields ";
            $sSelect .= " , `oevattbe_countryvatgroups`.`oevattbe_rate` ";
            $sSelect .= " from oxactions2article";
            $sSelect .= " left join $sArticleTable on $sArticleTable.oxid = oxactions2article.oxartid";
            $sSelect .= " left join $sViewName on $sViewName.oxid = oxactions2article.oxactionid";
            $sSelect .= " LEFT JOIN `oevattbe_articlevat` ON `" . $sArticleTable . "`.`oxid` = `oevattbe_articlevat`.`oevattbe_articleid` ";
            $sSelect .= "       AND `oevattbe_articlevat`.`oevattbe_countryid` = " . oxDb::getDb()->quote($this->_getTbeCountryId());
            $sSelect .= " LEFT JOIN `oevattbe_countryvatgroups` ON `oevattbe_articlevat`.`oevattbe_vatgroupid` = `oevattbe_countryvatgroups`.`oevattbe_id` ";
            $sSelect .= " where oxactions2article.oxshopid = '$sShopID' and oxactions2article.oxactionid = $sActionID and $sActiveSql";
            $sSelect .= " and $sArticleTable.oxid is not null and " . $oBaseObject->getSqlActiveSnippet();
            $sSelect .= " order by oxactions2article.oxsort $sLimit";

            $this->selectString($sSelect);
        } else {
            parent::loadActionArticles($sActionID, $iLimit = null);
        }

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
        if (!is_null($this->_getTbeCountryId())) {
            $myConfig = $this->getConfig();

            // Performance
            if (!$myConfig->getConfigParam('bl_perfLoadAccessoires')) {
                return;
            }

            $sArticleId = oxDb::getDb()->quote($sArticleId);

            $oBaseObject = $this->getBaseObject();
            $sArticleTable = $oBaseObject->getViewName();

            $sSelect = "select $sArticleTable.* ";
            $sSelect .= " , `oevattbe_countryvatgroups`.`oevattbe_rate` ";
            $sSelect .= " from oxaccessoire2article ";
            $sSelect .= " left join $sArticleTable on oxaccessoire2article.oxobjectid=$sArticleTable.oxid ";
            $sSelect .= " LEFT JOIN `oevattbe_articlevat` ON `" . $sArticleTable . "`.`oxid` = `oevattbe_articlevat`.`oevattbe_articleid` ";
            $sSelect .= "       AND `oevattbe_articlevat`.`oevattbe_countryid` = " . oxDb::getDb()->quote($this->_getTbeCountryId());
            $sSelect .= " LEFT JOIN `oevattbe_countryvatgroups` ON `oevattbe_articlevat`.`oevattbe_vatgroupid` = `oevattbe_countryvatgroups`.`oevattbe_id` ";
            $sSelect .= "where oxaccessoire2article.oxarticlenid = $sArticleId ";
            $sSelect .= " and $sArticleTable.oxid is not null and " . $oBaseObject->getSqlActiveSnippet();
            //sorting articles
            $sSelect .= " order by oxaccessoire2article.oxsort";

            $this->selectString($sSelect);
        } else {
            parent::loadArticleAccessoires($sArticleId);
        }

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
        if (!is_null($this->_getTbeCountryId())) {
            $myConfig = $this->getConfig();

            // Performance
            if (!$myConfig->getConfigParam('bl_perfLoadCrossselling')) {
                return null;
            }

            $oBaseObject = $this->getBaseObject();
            $sArticleTable = $oBaseObject->getViewName();

            $sArticleId = oxDb::getDb()->quote($sArticleId);

            $sSelect = "SELECT $sArticleTable.* ";
            $sSelect .= " , `oevattbe_countryvatgroups`.`oevattbe_rate` ";
            $sSelect .= " FROM $sArticleTable ";
            $sSelect .= " INNER JOIN oxobject2article ON oxobject2article.oxobjectid=$sArticleTable.oxid ";
            $sSelect .= " LEFT JOIN `oevattbe_articlevat` ON `" . $sArticleTable . "`.`oxid` = `oevattbe_articlevat`.`oevattbe_articleid` ";
            $sSelect .= "       AND `oevattbe_articlevat`.`oevattbe_countryid` = " . oxDb::getDb()->quote($this->_getTbeCountryId());
            $sSelect .= " LEFT JOIN `oevattbe_countryvatgroups` ON `oevattbe_articlevat`.`oevattbe_vatgroupid` = `oevattbe_countryvatgroups`.`oevattbe_id` ";
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

        } else {
            parent::loadArticleCrossSell($sArticleId);
        }

    }

    /**
     * Loads newest shops articles from DB.
     *
     * @param int $iLimit Select limit
     */
    public function loadNewestArticles($iLimit = null)
    {
        if (!is_null($this->_getTbeCountryId())) {
            $myConfig = $this->getConfig();

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

                    $sArticleTable = getViewName('oxarticles');
                    if ($myConfig->getConfigParam('blNewArtByInsert')) {
                        $sType = 'oxinsert';
                    } else {
                        $sType = 'oxtimestamp';
                    }
                    $sSelect = "select $sArticleTable.* ";
                    $sSelect .= " , `oevattbe_countryvatgroups`.`oevattbe_rate` ";
                    $sSelect .= " from $sArticleTable ";
                    $sSelect .= " LEFT JOIN `oevattbe_articlevat` ON `" . $sArticleTable . "`.`oxid` = `oevattbe_articlevat`.`oevattbe_articleid` ";
                    $sSelect .= "       AND `oevattbe_articlevat`.`oevattbe_countryid` = " . oxDb::getDb()->quote($this->_getTbeCountryId());
                    $sSelect .= " LEFT JOIN `oevattbe_countryvatgroups` ON `oevattbe_articlevat`.`oevattbe_vatgroupid` = `oevattbe_countryvatgroups`.`oevattbe_id` ";
                    $sSelect .= "where oxparentid = '' and " . $this->getBaseObject()->getSqlActiveSnippet() . " and oxissearch = 1 order by $sType desc ";
                    if (!($iLimit = (int) $iLimit)) {
                        $iLimit = $myConfig->getConfigParam('iNrofNewcomerArticles');
                    }
                    $sSelect .= "limit " . $iLimit;

                    $this->selectString($sSelect);

                    break;
            }
        } else {
            parent::loadNewestArticles($iLimit);
        }

    }

    /**
     * Load top 5 articles
     *
     * @param int $iLimit Select limit
     */
    public function loadTop5Articles($iLimit = null)
    {
        if (!is_null($this->_getTbeCountryId())) {
            $myConfig = $this->getConfig();

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
                    $sArticleTable = getViewName('oxarticles');

                    //by default limit 5
                    $sLimit = ($iLimit > 0) ? "limit " . $iLimit : 'limit 5';

                    $sSelect = "select $sArticleTable.* ";
                    $sSelect .= " , `oevattbe_countryvatgroups`.`oevattbe_rate` ";
                    $sSelect .= " from $sArticleTable ";
                    $sSelect .= " LEFT JOIN `oevattbe_articlevat` ON `" . $sArticleTable . "`.`oxid` = `oevattbe_articlevat`.`oevattbe_articleid` ";
                    $sSelect .= "       AND `oevattbe_articlevat`.`oevattbe_countryid` = " . oxDb::getDb()->quote($this->_getTbeCountryId());
                    $sSelect .= " LEFT JOIN `oevattbe_countryvatgroups` ON `oevattbe_articlevat`.`oevattbe_vatgroupid` = `oevattbe_countryvatgroups`.`oevattbe_id` ";
                    $sSelect .= "where " . $this->getBaseObject()->getSqlActiveSnippet() . " and $sArticleTable.oxissearch = 1 ";
                    $sSelect .= "and $sArticleTable.oxparentid = '' and $sArticleTable.oxsoldamount>0 ";
                    $sSelect .= "order by $sArticleTable.oxsoldamount desc $sLimit";

                    $this->selectString($sSelect);
                    break;
            }
        } else {
            parent::loadTop5Articles($iLimit);
        }

    }

    /**
     * Returns the appropriate SQL select
     *
     * @param string $sRecommId       Recommlist ID
     * @param string $sArticlesFilter Additional filter for recommlist's items
     *
     * @return string
     */
    protected function _getArticleSelect($sRecommId, $sArticlesFilter = null)
    {
        if (!is_null($this->_getTbeCountryId())) {
            $sRecommId = oxDb::getDb()->quote($sRecommId);

            $sArticleTable = getViewName('oxarticles');

            $sSelect = "select distinct $sArticleTable.*, oxobject2list.oxdesc ";
            $sSelect .= " , `oevattbe_countryvatgroups`.`oevattbe_rate` ";
            $sSelect .= " from oxobject2list ";
            $sSelect .= "left join $sArticleTable on oxobject2list.oxobjectid = $sArticleTable.oxid ";
            $sSelect .= " LEFT JOIN `oevattbe_articlevat` ON `" . $sArticleTable . "`.`oxid` = `oevattbe_articlevat`.`oevattbe_articleid` ";
            $sSelect .= "       AND `oevattbe_articlevat`.`oevattbe_countryid` = " . oxDb::getDb()->quote($this->_getTbeCountryId());
            $sSelect .= " LEFT JOIN `oevattbe_countryvatgroups` ON `oevattbe_articlevat`.`oevattbe_vatgroupid` = `oevattbe_countryvatgroups`.`oevattbe_id` ";
            $sSelect .= "where (oxobject2list.oxlistid = $sRecommId) " . $sArticlesFilter;

            return $sSelect;
        } else {
            return parent::_getArticleSelect($sRecommId, $sArticlesFilter);
        }

    }

    /**
     * Returns users tbe country
     *
     * @return string
     */
    private function _getTbeCountryId()
    {
        $sCountryId = null;
        $oUser = $this->getBaseObject()->getUser();

        if ($oUser) {
            $sCountryId = $oUser->getTbeCountryId();
        }

        return $sCountryId;
    }
}
