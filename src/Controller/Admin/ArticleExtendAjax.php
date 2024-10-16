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
            ContainerFacade::get(CategoryArticlesUpdater::class)
                ->addCategoryTBEInformationToArticles($oCategory);
        }
    }
}
