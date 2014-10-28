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
 * VAT TBE oxArticle class.
 */
class oeVATTBEOxArticle extends oeVATTBEOxArticle_parent
{
    /** @var oeVATTBETBEArticle */
    private $_oVATTBEArticle = null;

    /** @var oeVATTBEArticleSQLBuilder */
    private $_oVATTBEArticleSQLBuilder = null;

    /**
     * Article TBE vat.
     *
     * @return string
     */
    public function oeVATTBEgetTBEVat()
    {
        return $this->oxarticles__oevattbe_rate->value;
    }

    /**
     * Article TBE vat.
     *
     * @return int
     */
    public function oeVATTBEisTBEService()
    {
        return $this->oxarticles__oevattbe_istbeservice->value;
    }

    /**
     * Generate cache keys for dependent cached data.
     * Add user country for TBE articles.
     *
     * @param array $aLanguages lang id array
     * @param array $aShops     shop ids array
     *
     * @return string
     */
    public function getCacheKeys($aLanguages = null, $aShops = null)
    {
        $aKeys = parent::getCacheKeys($aLanguages, $aShops);

        $oTBEArticleCacheKey = $this->_getVATTBETBEArticleCacheKey();
        if ($this->oeVATTBEisTBEService() && $oTBEArticleCacheKey->needToCalculateKeys()) {
            $aKeys = $oTBEArticleCacheKey->updateCacheKeys($aKeys);
        }

        return $aKeys;
    }


    /**
     * Builds and returns SQL query string.
     *
     * @param mixed $aWhere SQL select WHERE conditions array (default false)
     *
     * @return string
     */
    public function buildSelectString($aWhere = null)
    {
        if (!$this->_isTBEConfigured()) {
            return parent::buildSelectString($aWhere);
        }

        $sSelect = "SELECT ";
        $sSelect .= $this->_getVATTBEArticleSqlBuilder()->getSelectFields();
        $sSelect .= " FROM " . $this->getViewName();
        $sSelect .= $this->_getVATTBEArticleSqlBuilder()->getJoins();
        $sSelect .= " WHERE 1 ";

        if ($aWhere) {
            reset($aWhere);
            while (list($name, $value) = each($aWhere)) {
                $sSelect .= ' and ' . $name . ' = ' . oxDb::getDb()->quote($value);
            }
        }

        // add active shop
        if (oxRegistry::getConfig()->getEdition() == 'EE') {
            if ($this->getShopId() && $this->_blDisableShopCheck === false) {
                $sLongFieldName = $this->_getFieldLongName('oxshopid');
                if (isset($this->$sLongFieldName)) {
                    $sFieldName = $this->getViewName() . ".oxshopid";
                    if (!isset($aWhere[$sFieldName])) {
                        $sSelect .= " and $sFieldName = '" . $this->getShopId() . "'";
                    }
                }
            }
        }

        return $sSelect;
    }

    /**
     * Returns users tbe country.
     *
     * @return string
     */
    private function _getTbeCountryId()
    {
        $sCountryId = null;
        $oUser = $this->getUser();

        if ($oUser) {
            $sCountryId = $oUser->getTbeCountryId();
        }

        return $sCountryId;
    }

    /**
     * Returns users tbe country
     *
     * @return string
     */
    private function _isTBEConfigured()
    {
        $isConfigured = false;
        $sCountryId = $this->_getTbeCountryId();
        if (!is_null($sCountryId)) {
            $oCountry = oxNew('oxCountry');
            $oCountry->load($sCountryId);
            $isConfigured = $oCountry->appliesTBEVAT();
        }

        return $isConfigured;
    }


    /**
     * Returns TBE Article object.
     *
     * @return oeVATTBETBEArticleCacheKey
     */
    protected function _getVATTBETBEArticleCacheKey()
    {
        if (!$this->_oVATTBEArticle) {
            $this->_oVATTBEArticle = oxNew('oeVATTBETBEArticleCacheKey', $this->getUser());
        }

        return $this->_oVATTBEArticle;
    }

    /**
     * Article sql builder.
     *
     * @return oeVATTBEArticleSQLBuilder
     */
    protected function _getVATTBEArticleSqlBuilder()
    {
        if (is_null($this->_oVATTBEArticleSQLBuilder)) {
            $this->_oVATTBEArticleSQLBuilder = oxNew('oeVATTBEArticleSQLBuilder', $this);
        }

        return $this->_oVATTBEArticleSQLBuilder;
    }
}
