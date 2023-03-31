<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
