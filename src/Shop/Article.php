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

use OxidEsales\EVatModule\Model\ArticleSQLBuilder;
use OxidEsales\EVatModule\Model\ArticleCacheKey;
use OxidEsales\EVatModule\Model\User;
use \oxDb;
use OxidEsales\Facts\Facts;

/**
 * VAT TBE oxArticle class.
 */
class Article extends Article_parent
{
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

        $oUserCountry = User::createInstance();
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
