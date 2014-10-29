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
 * VAT TBE oxArticle class
 */
class oeVATTBEOxSearch extends oeVATTBEOxSearch_parent
{
    /** @var oeVATTBEArticleSQLBuilder */
    private $_oVATTBEArticleSQLBuilder = null;

    /**
     * Returns the appropriate SQL select for a search according to search parameters
     *
     * @param string $sSearchParamForQuery       query parameter
     * @param string $sInitialSearchCat          initial category to search in
     * @param string $sInitialSearchVendor       initial vendor to search for
     * @param string $sInitialSearchManufacturer initial Manufacturer to search for
     * @param string $sSortBy                    sort by
     *
     * @return string
     */
    protected function _getSearchSelect($sSearchParamForQuery = false, $sInitialSearchCat = false, $sInitialSearchVendor = false, $sInitialSearchManufacturer = false, $sSortBy = false)
    {
        if (!$this->_isOeVATTBEConfigured()) {
            return parent::_getSearchSelect($sSearchParamForQuery, $sInitialSearchCat, $sInitialSearchVendor, $sInitialSearchManufacturer, $sSortBy);
        }

        $oDb = oxDb::getDb();

        // performance
        if ($sInitialSearchCat) {
            // lets search this category - is no such category - skip all other code
            $oCategory = oxNew('oxcategory');
            $sCatTable = $oCategory->getViewName();

            $sQ = "select 1 from $sCatTable where $sCatTable.oxid = " . $oDb->quote($sInitialSearchCat) . " ";
            $sQ .= "and " . $oCategory->getSqlActiveSnippet();
            if (!$oDb->getOne($sQ)) {
                return;
            }
        }

        // performance:
        if ($sInitialSearchVendor) {
            // lets search this vendor - if no such vendor - skip all other code
            $oVendor = oxNew('oxvendor');
            $sVndTable = $oVendor->getViewName();

            $sQ = "select 1 from $sVndTable where $sVndTable.oxid = " . $oDb->quote($sInitialSearchVendor) . " ";
            $sQ .= "and " . $oVendor->getSqlActiveSnippet();
            if (!$oDb->getOne($sQ)) {
                return;
            }
        }

        // performance:
        if ($sInitialSearchManufacturer) {
            // lets search this Manufacturer - if no such Manufacturer - skip all other code
            $oManufacturer = oxNew('oxmanufacturer');
            $sManTable = $oManufacturer->getViewName();

            $sQ = "select 1 from $sManTable where $sManTable.oxid = " . $oDb->quote($sInitialSearchManufacturer) . " ";
            $sQ .= "and " . $oManufacturer->getSqlActiveSnippet();
            if (!$oDb->getOne($sQ)) {
                return;
            }
        }

        $sWhere = null;

        if ($sSearchParamForQuery) {
            $sWhere = $this->_getWhere($sSearchParamForQuery);
        } elseif (!$sInitialSearchCat && !$sInitialSearchVendor && !$sInitialSearchManufacturer) {
            //no search string
            return null;
        }

        $oArticle = oxNew('oxarticle');
        $sArticleTable = $oArticle->getViewName();
        $sO2CView = getViewName('oxobject2category');

        $sSelectFields = $this->_getVATTBEArticleSqlBuilder()->getSelectFields();


        // longdesc field now is kept on different table
        $sDescJoin = '';
        if (is_array($aSearchCols = $this->getConfig()->getConfigParam('aSearchCols'))) {
            if (in_array('oxlongdesc', $aSearchCols) || in_array('oxtags', $aSearchCols)) {
                $sDescView = getViewName('oxartextends', $this->_iLanguage);
                $sDescJoin = " LEFT JOIN {$sDescView} ON {$sArticleTable}.oxid={$sDescView}.oxid ";
            }
        }

        $sDescJoin .= $this->_oVATTBEArticleSQLBuilder->getJoins();

        //select articles
        $sSelect = "select {$sSelectFields}, {$sArticleTable}.oxtimestamp from {$sArticleTable} {$sDescJoin} where ";

        // must be additional conditions in select if searching in category
        if ($sInitialSearchCat) {
            $sCatView = getViewName('oxcategories', $this->_iLanguage);
            $sInitialSearchCatQuoted = $oDb->quote($sInitialSearchCat);
            $sSelectCat = "select oxid from {$sCatView} where oxid = $sInitialSearchCatQuoted and (oxpricefrom != '0' or oxpriceto != 0)";
            if ($oDb->getOne($sSelectCat)) {
                $sSelect = "select {$sSelectFields}, {$sArticleTable}.oxtimestamp from {$sArticleTable} $sDescJoin " .
                           "where {$sArticleTable}.oxid in ( select {$sArticleTable}.oxid as id from {$sArticleTable}, {$sO2CView} as oxobject2category, {$sCatView} as oxcategories " .
                           "where (oxobject2category.oxcatnid=$sInitialSearchCatQuoted and oxobject2category.oxobjectid={$sArticleTable}.oxid) or (oxcategories.oxid=$sInitialSearchCatQuoted and {$sArticleTable}.oxprice >= oxcategories.oxpricefrom and
                        {$sArticleTable}.oxprice <= oxcategories.oxpriceto )) and ";
            } else {
                $sSelect = "select {$sSelectFields} from {$sO2CView} as
                        oxobject2category, {$sArticleTable} {$sDescJoin} where oxobject2category.oxcatnid=$sInitialSearchCatQuoted and
                        oxobject2category.oxobjectid={$sArticleTable}.oxid and ";
            }
        }

        $sSelect .= $oArticle->getSqlActiveSnippet();
        $sSelect .= " and {$sArticleTable}.oxparentid = '' and {$sArticleTable}.oxissearch = 1 ";

        if ($sInitialSearchVendor) {
            $sSelect .= " and {$sArticleTable}.oxvendorid = " . $oDb->quote($sInitialSearchVendor) . " ";
        }

        if ($sInitialSearchManufacturer) {
            $sSelect .= " and {$sArticleTable}.oxmanufacturerid = " . $oDb->quote($sInitialSearchManufacturer) . " ";
        }

        $sSelect .= $sWhere;

        if ($sSortBy) {
            $sSelect .= " order by {$sSortBy} ";
        }


        return $sSelect;
    }

    /**
     * Returns users tbe country
     *
     * @return string
     */
    private function _getOeVATTBECountryId()
    {
        $sCountryId = null;
        $oUser = $this->getUser();

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
    private function _isOeVATTBEConfigured()
    {
        $isConfigured = false;
        $sCountryId = $this->_getOeVATTBECountryId();
        if (!is_null($sCountryId)) {
            $oCountry = oxNew('oxCountry');
            $oCountry->load($sCountryId);
            $isConfigured = $oCountry->appliesTBEVAT();
        }

        return $isConfigured;
    }

    /**
     * Article sql builder
     *
     * @return oeVATTBEArticleSQLBuilder
     */
    protected function _getVATTBEArticleSqlBuilder()
    {
        if (is_null($this->_oVATTBEArticleSQLBuilder)) {
            $oArticle = oxNew('oxarticle');
            $oArticle->setUser($this->getUser());
            $this->_oVATTBEArticleSQLBuilder = oxNew('oeVATTBEArticleSQLBuilder', $oArticle);
        }

        return $this->_oVATTBEArticleSQLBuilder;
    }
}
