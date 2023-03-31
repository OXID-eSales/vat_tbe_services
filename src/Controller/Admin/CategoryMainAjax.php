<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Category as EShopCategory;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\CategoryArticlesUpdater;
use OxidEsales\EVatModule\Shop\Category;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * Adds additional functionality needed for oeVATTBE module when managing articles.
 */
class CategoryMainAjax extends CategoryMainAjax_parent
{
    use ServiceContainer;

    /**
     * Adds article to category.
     * Creates new list.
     */
    public function addArticle()
    {
        parent::addArticle();
        $this->populateOeVATTBEConfiguration();
    }

    /**
     * Removes article from category.
     */
    public function removeArticle()
    {
        $aArticles = $this->getActionIds('oxarticles.oxid');
        if (Registry::getRequest()->getRequestParameter('all')) {
            $sArticleTable = $this->getViewName('oxarticles');
            $aArticles = $this->getAll($this->addFilter("select $sArticleTable.oxid " . $this->getQuery()));
        }

        $this
            ->getServiceFromContainer(CategoryArticlesUpdater::class)
            ->removeCategoryTBEInformationFromArticles($aArticles);

        parent::removeArticle();
    }

    /**
     * Populates VAT groups configuration.
     */
    protected function populateOeVATTBEConfiguration()
    {
        $sCategoryId = Registry::getRequest()->getRequestParameter('synchoxid');
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
