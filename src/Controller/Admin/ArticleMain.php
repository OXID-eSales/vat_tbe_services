<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Category as EShopCategory;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EVatModule\Model\CategoryArticlesUpdater;
use OxidEsales\EVatModule\Shop\Category;

/**
 * Adds additional functionality needed for oeVATTBE module when managing articles.
 */
class ArticleMain extends ArticleMain_parent
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
            ContainerFacade::get(CategoryArticlesUpdater::class)
                ->addCategoryTBEInformationToArticles($oCategory);
        }
    }
}
