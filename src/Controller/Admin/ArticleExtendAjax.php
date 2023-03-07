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

use OxidEsales\Eshop\Application\Model\Category as EShopCategory;
use OxidEsales\EVatModule\Model\CategoryArticlesUpdater;
use OxidEsales\EVatModule\Shop\Category;

/**
 * Adds additional functionality needed for oeVATTBE module when managing articles.
 */
class ArticleExtendAjax extends ArticleExtendAjax_parent
{
    /**
     * Adds article to category
     * Creates new list
     */
    public function addCat()
    {
        parent::addCat();
        $aCategories = $this->getActionIds('oxcategories.oxid');
        foreach ($aCategories as $sCategoryId) {
            $this->populateOeVATTBEConfiguration($sCategoryId);
        }
    }

    /**
     * Populates VAT groups configuration
     *
     * @param string $sCategoryId categoryId
     */
    protected function populateOeVATTBEConfiguration($sCategoryId)
    {
        /** @var EShopCategory|Category $oCategory */
        $oCategory = oxNew(EShopCategory::class);
        $oCategory->load($sCategoryId);
        if ($oCategory->isOeVATTBETBE()) {
            CategoryArticlesUpdater::createInstance()->addCategoryTBEInformationToArticles($oCategory);
        }
    }
}
