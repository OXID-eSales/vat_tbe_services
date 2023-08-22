<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Category as EShopCategory;
use OxidEsales\EVatModule\Model\CategoryArticlesUpdater;
use OxidEsales\EVatModule\Shop\Category;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * Adds additional functionality needed for oeVATTBE module when managing articles.
 */
class ArticleMain extends ArticleMain_parent
{
    use ServiceContainer;

    /**
     * Add article to category.
     *
     * @param string $sCatID Category id
     * @param string $sOXID  Article id
     */
    public function addToCategory($sCatID, $sOXID)
    {
        parent::addToCategory($sCatID, $sOXID);
        $this->populateOeVATTBEConfiguration($sCatID);
    }

    /**
     * Populates VAT groups configuration
     *
     * @param string $sCategoryId category id
     */
    protected function populateOeVATTBEConfiguration($sCategoryId)
    {
        /** @var EShopCategory|Category $oCategory */
        $oCategory = oxNew(EShopCategory::class);
        $oCategory->load($sCategoryId);
        if ($oCategory->isOeVATTBETBE()) {
            $this
                ->getServiceFromContainer(CategoryArticlesUpdater::class)
                ->addCategoryTBEInformationToArticles($oCategory);
        }
    }
}
