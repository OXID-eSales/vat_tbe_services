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

namespace OxidEsales\EVatModule\Model;

use OxidEsales\Eshop\Application\Model\Article;
use \oxDb;

/**
 * Has TBE TBE article logic.
 */
class oeVATTBEArticleSQLBuilder
{

    private $_oArticle;

    /**
     * Constructor
     *
     * @param Article $oArticle article
     */
    public function __construct(Article $oArticle)
    {
        $this->_oArticle = $oArticle;
    }

    /**
     * Return part of sql: select field list
     *
     * @return string
     */
    public function getSelectFields()
    {
        $sSelect = '';
        $sSelect .=  $this->_oArticle->getSelectFields();
        $sSelect .= ", `oevattbe_countryvatgroups`.`oevattbe_rate` ";

        return $sSelect;
    }

    /**
     * Return part of sql: select field list
     *
     * @return string
     */
    public function getJoins()
    {
        $oArticle = $this->_oArticle;

        $oUser = $oArticle->getUser();

        $sSelect = '';
        $sSelect .= " LEFT JOIN `oevattbe_articlevat` ON `".$oArticle->getViewName()."`.`oxid` = `oevattbe_articlevat`.`oevattbe_articleid` ";
        if ($oUser) {
            $sSelect .= " AND `oevattbe_articlevat`.`oevattbe_countryid` = " . oxDb::getDb()->quote($oUser->getOeVATTBETbeCountryId());
        }
        $sSelect .= " LEFT JOIN `oevattbe_countryvatgroups` ON `oevattbe_articlevat`.`oevattbe_VATGROUPID` = `oevattbe_countryvatgroups`.`oevattbe_id` ";

        return $sSelect;
    }
}
