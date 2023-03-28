<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
        oeVATTBECategoryArticlesUpdater::createInstance()->removeCategoryTBEInformationFromArticles($aArticles);
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
            oeVATTBECategoryArticlesUpdater::createInstance()->addCategoryTBEInformationToArticles($oCategory);
        }
    }
}
