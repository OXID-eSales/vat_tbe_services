<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Adds additional functionality needed for oeVATTBE module when managing articles.
 */
class oeVATTBEArticle_Extend_Ajax extends oeVATTBEArticle_Extend_Ajax_parent
{
    /**
     * Adds article to category
     * Creates new list
     */
    public function addCat()
    {
        parent::addCat();
        $aCategories = $this->_getActionIds('oxcategories.oxid');
        foreach ($aCategories as $sCategoryId) {
            $this->_populateOeVATTBEConfiguration($sCategoryId);
        }
    }

    /**
     * Populates VAT groups configuration
     *
     * @param string $sCategoryId categoryId
     */
    protected function _populateOeVATTBEConfiguration($sCategoryId)
    {
        /** @var oxCategory|oeVATTBEOxCategory $oCategory */
        $oCategory = oxNew('oxCategory');
        $oCategory->load($sCategoryId);
        if ($oCategory->isOeVATTBETBE()) {
            oeVATTBECategoryArticlesUpdater::createInstance()->addCategoryTBEInformationToArticles($oCategory);
        }
    }
}
