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
class oeVATTBECategory_Main_Ajax extends oeVATTBECategory_Main_Ajax_parent
{
    /**
     * Adds article to category.
     * Creates new list.
     */
    public function addArticle()
    {
        parent::addArticle();
        $this->_populateOeVATTBEConfiguration();
    }

    /**
     * Removes article from category.
     */
    public function removeArticle()
    {
        $aArticles = $this->_getActionIds('oxarticles.oxid');
        if (oxRegistry::getConfig()->getRequestParameter('all')) {
            $sArticleTable = $this->_getViewName('oxarticles');
            $aArticles = $this->_getAll($this->_addFilter("select $sArticleTable.oxid " . $this->_getQuery()));
        }
        oeVATTBECategoryVATGroupsPopulator::createInstance()->reset($aArticles);
        parent::removeArticle();
    }

    /**
     * Populates VAT groups configuration.
     */
    protected function _populateOeVATTBEConfiguration()
    {
        $sCategoryId = oxRegistry::getConfig()->getRequestParameter('synchoxid');
        /** @var oxCategory|oeVATTBEOxCategory $oCategory */
        $oCategory = oxNew('oxCategory');
        $oCategory->load($sCategoryId);
        if ($oCategory->isOeVATTBETBE()) {
            oeVATTBECategoryVATGroupsPopulator::createInstance()->populate($oCategory);
        }
    }
}
