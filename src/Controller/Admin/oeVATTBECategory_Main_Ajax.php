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

namespace OxidEsales\EVatModule\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\oeVATTBECategoryArticlesUpdater;
use OxidEsales\EVatModule\Shop\oeVATTBEOxCategory;

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
        if (Registry::getRequest()->getRequestParameter('all')) {
            $sArticleTable = $this->_getViewName('oxarticles');
            $aArticles = $this->_getAll($this->_addFilter("select $sArticleTable.oxid " . $this->_getQuery()));
        }
        oeVATTBECategoryArticlesUpdater::createInstance()->removeCategoryTBEInformationFromArticles($aArticles);
        parent::removeArticle();
    }

    /**
     * Populates VAT groups configuration.
     */
    protected function _populateOeVATTBEConfiguration()
    {
        $sCategoryId = Registry::getRequest()->getRequestParameter('synchoxid');
        /** @var Category|oeVATTBEOxCategory $oCategory */
        $oCategory = oxNew(Category::class);
        $oCategory->load($sCategoryId);
        if ($oCategory->isOeVATTBETBE()) {
            oeVATTBECategoryArticlesUpdater::createInstance()->addCategoryTBEInformationToArticles($oCategory);
        }
    }
}
