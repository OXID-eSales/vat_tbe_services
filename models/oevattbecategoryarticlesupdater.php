<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * VAT Groups handling class
 */
class oeVATTBECategoryArticlesUpdater
{
    /**
     * Handles class dependencies.
     *
     * @param oeVATTBECategoryVATGroupsPopulatorDbGateway $oDbGateway db gateway
     */
    public function __construct($oDbGateway)
    {
        $this->_oDbGateway = $oDbGateway;
    }

    /**
     * Creates an instance of oeVATTBECategoryArticlesUpdater.
     *
     * @return oeVATTBECategoryArticlesUpdater
     */
    public static function createInstance()
    {
        $oGateway = oxNew('oeVATTBECategoryVATGroupsPopulatorDbGateway');
        $oList = oxNew('oeVATTBECategoryArticlesUpdater', $oGateway);

        return $oList;
    }

    /**
     * Method for model saving (insert and update data).
     *
     * @param oxCategory|oeVATTBEOxCategory $oCategory category
     */
    public function addCategoryTBEInformationToArticles($oCategory)
    {
        $this->_getDbGateway()->populate($oCategory->getId());
    }

    /**
     * Resets articles to be not TBE services.
     *
     * @param array $aArticles
     */
    public function removeCategoryTBEInformationFromArticles($aArticles)
    {
        $this->_getDbGateway()->reset($aArticles);
    }

    /**
     * Returns model database gateway.
     *
     * @return oeVATTBECategoryVATGroupsPopulatorDbGateway
     */
    protected function _getDbGateway()
    {
        return $this->_oDbGateway;
    }
}
