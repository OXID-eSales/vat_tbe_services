<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Shop;

use OxidEsales\EVatModule\Model\ArticleSQLBuilder;
use OxidEsales\EVatModule\Model\ArticleCacheKey;
use OxidEsales\EVatModule\Model\User;
use \oxDb;
use OxidEsales\EVatModule\Traits\ServiceContainer;
use OxidEsales\Facts\Facts;

/**
 * VAT TBE oxArticle class.
 */
class Article extends Article_parent
{
    use ServiceContainer;

    /** @var ArticleCacheKey */
    private $_oVATTBEArticle = null;

    /** @var ArticleSQLBuilder */
    private $_oVATTBEArticleSQLBuilder = null;

    /**
     * Article TBE vat.
     *
     * @return string
     */
    public function getOeVATTBETBEVat()
    {
        return $this->getFieldData('oevattbe_rate');
    }

    /**
     * Article TBE vat.
     *
     * @return int
     */
    public function isOeVATTBETBEService()
    {
        return $this->getFieldData('oevattbe_istbeservice');
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

        $oTBEArticleCacheKey = $this->getOeVATTBETBEArticleCacheKey();
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
        if (!$this->isOeVATTBEConfigured() || $this->isAdmin()) {
            return parent::buildSelectString($aWhere);
        }

        $sSelect = "SELECT ";
        $sSelect .= $this->getOeVATTBETBEArticleSqlBuilder()->getSelectFields();
        $sSelect .= " FROM " . $this->getViewName();
        $sSelect .= $this->getOeVATTBETBEArticleSqlBuilder()->getJoins();
        $sSelect .= " WHERE 1 ";

        if ($aWhere) {
            reset($aWhere);
            foreach ($aWhere as $name => $value) {
                $sSelect .= ' and ' . $name . ' = ' . oxDb::getDb()->quote($value);
            }
        }

        // add active shop
        if ((new Facts())->getEdition() == 'EE') {
            if ($this->getShopId() && $this->_blDisableShopCheck === false) {
                $sLongFieldName = $this->getFieldLongName('oxshopid');
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
    private function isOeVATTBEConfigured()
    {
        $isConfigured = false;

        $oUserCountry = $this->getServiceFromContainer(User::class);
        $oCountry = $oUserCountry->getCountry();
        if (!is_null($oCountry)) {
            $isConfigured = $oCountry->appliesOeTBEVATTbeVat();
        }

        return $isConfigured;
    }


    /**
     * Returns TBE Article object.
     *
     * @return ArticleCacheKey
     */
    protected function getOeVATTBETBEArticleCacheKey()
    {
        if (!$this->_oVATTBEArticle) {
            $this->_oVATTBEArticle = oxNew(ArticleCacheKey::class, $this->getUser());
        }

        return $this->_oVATTBEArticle;
    }

    /**
     * Article sql builder.
     *
     * @return ArticleSQLBuilder
     */
    protected function getOeVATTBETBEArticleSqlBuilder()
    {
        if (is_null($this->_oVATTBEArticleSQLBuilder)) {
            $this->_oVATTBEArticleSQLBuilder = oxNew(ArticleSQLBuilder::class, $this);
        }

        return $this->_oVATTBEArticleSQLBuilder;
    }
}
