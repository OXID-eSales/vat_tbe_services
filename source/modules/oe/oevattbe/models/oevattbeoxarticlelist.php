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
class oeVatTbeOxArticleList extends oeVatTbeOxArticleList_parent
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