<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Has TBE TBE article logic.
 */
class oeVATTBEArticleSQLBuilder
{

    private $_oArticle;

    /**
     * Constructor
     *
     * @param oxArticle $oArticle article
     */
    public function __construct($oArticle)
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
