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
class oeVATTBEOxArticle extends oeVATTBEOxArticle_parent
{
    /**
     * Article TBE vat
     *
     * @return string
     */
    public function getTBEVat()
    {
        return $this->oxarticles__oevattbe_rate->value;
    }

    /**
     * Article TBE vat
     *
     * @return string
     */
    public function isTBEService()
    {
        return $this->oxarticles__oevattbe_istbeservice->value;
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
        if (is_null($this->_getTbeCountryId())) {
            return parent::buildSelectString($aWhere);
        }

        $sSelect = "SELECT " . $this->getSelectFields();
        $sSelect .= " , `oevattbe_countryvatgroups`.`oevattbe_rate` ";
        $sSelect .= " FROM " . $this->getViewName();
        $sSelect .= " LEFT JOIN `oevattbe_articlevat` ON `".$this->getViewName()."`.`oxid` = `oevattbe_articlevat`.`oevattbe_articleid` ";
        $sSelect .= " AND `oevattbe_articlevat`.`oevattbe_countryid` = " . oxDb::getDb()->quote($this->_getTbeCountryId());
        $sSelect .= " LEFT JOIN `oevattbe_countryvatgroups` ON `oevattbe_articlevat`.`oevattbe_VATGROUPID` = `oevattbe_countryvatgroups`.`oevattbe_id` ";
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
}
