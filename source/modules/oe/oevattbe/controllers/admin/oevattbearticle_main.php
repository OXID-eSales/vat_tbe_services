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
 * Adds additional functionality needed for oeVATTBE module when managing articles.
 */
class oeVATTBEArticle_Main extends oeVATTBEArticle_Main_parent
{
    /**
     * Add article to category.
     *
     * @param string $sCatID Category id
     * @param string $sOXID  Article id
     */
    public function addToCategory($sCatID, $sOXID)
    {
        parent::addToCategory($sCatID, $sOXID);
        $this->_populateOeVATTBEConfiguration($sCatID);
    }

    /**
     * Populates VAT groups configuration
     *
     * @param string $sCategoryId category id
     */
    protected function _populateOeVATTBEConfiguration($sCategoryId)
    {
        /** @var oxCategory|oeVATTBEOxCategory $oCategory */
        $oCategory = oxNew('oxCategory');
        $oCategory->load($sCategoryId);
        if ($oCategory->isOeVATTBETBE()) {
            oeVATTBECategoryVATGroupsPopulator::createInstance()->populate($oCategory);
        }
    }
}
