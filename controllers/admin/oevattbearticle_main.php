<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Adds additional functionality needed for oeVATTBE module when managing articles.
 */
class oeVATTBEArticle_Main extends oeVATTBEArticle_Main_parent
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
        $this->_populateOeVATTBEConfiguration($sCatID);
    }

    /**
     * Populates VAT groups configuration
     *
     * @param string $sCategoryId category id
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
