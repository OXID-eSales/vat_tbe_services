<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
    public function getOeVATTBETBEVat()
    {
        return $this->oxarticles__oevattbe_rate->value;
    }

    /**
     * Article TBE vat.
     *
     * @return int
     */
    public function isOeVATTBETBEService()
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

        $oTBEArticleCacheKey = $this->_getOeVATTBETBEArticleCacheKey();
        if ($this->isOeVATTBETBEService() && $oTBEArticleCacheKey->needToCalculateKeys()) {
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
        if (!$this->_isOeVATTBEConfigured() || $this->isAdmin()) {
            return parent::buildSelectString($aWhere);
        }

        $sSelect = "SELECT ";
        $sSelect .= $this->_getOeVATTBETBEArticleSqlBuilder()->getSelectFields();
        $sSelect .= " FROM " . $this->getViewName();
        $sSelect .= $this->_getOeVATTBETBEArticleSqlBuilder()->getJoins();
        $sSelect .= " WHERE 1 ";

        if ($aWhere) {
            reset($aWhere);
            foreach ($aWhere as $name => $value) {
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
     * Returns users tbe country
     *
     * @return string
     */
    private function _isOeVATTBEConfigured()
    {
        $isConfigured = false;

        $oUserCountry = oeVATTBETBEUser::createInstance();
        $oCountry = $oUserCountry->getCountry();
        if (!is_null($oCountry)) {
            $isConfigured = $oCountry->appliesOeTBEVATTbeVat();
        }

        return $isConfigured;
    }


    /**
     * Returns TBE Article object.
     *
     * @return oeVATTBETBEArticleCacheKey
     */
    protected function _getOeVATTBETBEArticleCacheKey()
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
    protected function _getOeVATTBETBEArticleSqlBuilder()
    {
        if (is_null($this->_oVATTBEArticleSQLBuilder)) {
            $this->_oVATTBEArticleSQLBuilder = oxNew('oeVATTBEArticleSQLBuilder', $this);
        }

        return $this->_oVATTBEArticleSQLBuilder;
    }
}
