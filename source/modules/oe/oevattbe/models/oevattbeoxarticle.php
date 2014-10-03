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
class oeVatTbeOxArticle extends oeVatTbeOxArticle_parent
{
    /**
     * Article TBE vat
     *
     * @return string
     */
    public function getTbeVat()
    {
        return $this->oxarticles__oevattbe_rate->value;
    }


    /**
     * Builds and returns SQL query string.
     *
     * @param mixed $aWhere SQL select WHERE conditions array (default false)
     *
     * @return string
     */
    public function buildArticleSelect($aWhere = null)
    {
        $sSelect = "SELECT " . $this->getSelectFields();
        $sSelect .= " , `oevattbe_countryvatgroups`.`OEVATTBE_RATE` ";
        $sSelect .= " FROM " . $this->getViewName();
        $sSelect .= " LEFT JOIN `oevattbe_articlevat` ON `".$this->getViewName()."`.`oxid` = `oevattbe_articlevat`.`OEVATTBE_ARTICLEID` ";
        $sSelect .= " AND `oevattbe_articlevat`.`OEVATTBE_COUNTRYID` = " . oxDb::getDb()->quote($this->_getTbeCountryId());
        $sSelect .= " LEFT JOIN `oevattbe_countryvatgroups` ON `oevattbe_articlevat`.`OEVATTBE_VATGROUPID` = `oevattbe_countryvatgroups`.`OEVATTBE_ID` ";
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
     * Get data from db
     *
     * @param string $sOxId id
     *
     * @return array
     */
    protected function _loadFromDb($sOxId)
    {
        $sTbeCountry = $this->_getTbeCountryId();

        if (!is_null($sTbeCountry)) {
            if (oxRegistry::getConfig()->getEdition() == 'EE') {
                $blCoreTableUsage = $this->getForceCoreTableUsage();
                $this->_forceCoreTableUsageForSharedBasket();
            }

            $sSelect = $this->buildArticleSelect(array($this->getViewName() . ".oxid" => $sOxId));

            if (oxRegistry::getConfig()->getEdition() == 'EE') {
                $this->setForceCoreTableUsage($blCoreTableUsage);
            }
            $aData = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getRow($sSelect);
        } else {
            $aData = parent::_loadFromDb($sOxId);
        }

        return $aData;
    }


    /**
     * Returns users tbe country
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
     * Sets forcing of core table usage for creating table view name when shared basket is enabled.
     */
    private function _forceCoreTableUsageForSharedBasket()
    {
        if ($this->getConfig()->getConfigParam('blMallSharedBasket')) {
            $this->setForceCoreTableUsage(true);
        }
    }
}
