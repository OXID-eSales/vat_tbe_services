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

use OxidEsales\Eshop\Application\Model\Category as EShopCategory;
use OxidEsales\EVatModule\Model\DbGateway\CategoryVATGroupsPopulatorDbGateway;
use OxidEsales\EVatModule\Shop\Category;

/**
 * VAT Groups handling class
 */
class CategoryArticlesUpdater
{
    /**
     * Handles class dependencies.
     *
     * @param CategoryVATGroupsPopulatorDbGateway $dbGateway db gateway
     */
    public function __construct(
        private CategoryVATGroupsPopulatorDbGateway $dbGateway
    )
    {
    }

    /**
     * Method for model saving (insert and update data).
     *
     * @param EShopCategory|Category $oCategory category
     */
    public function addCategoryTBEInformationToArticles($oCategory)
    {
        $this->dbGateway->populate($oCategory->getId());
    }

    /**
     * Resets articles to be not TBE services.
     *
     * @param array $aArticles
     */
    public function removeCategoryTBEInformationFromArticles($aArticles)
    {
        $this->dbGateway->reset($aArticles);
    }
}
