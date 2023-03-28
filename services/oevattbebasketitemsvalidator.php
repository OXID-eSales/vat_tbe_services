<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Store logic how to find articles with wrong TBE VAT.
 */
class oeVATTBEBasketItemsValidator
{
    /** @var oeVATTBEOrderArticleChecker */
    private $_oVATTBEOrderArticleChecker;

    /** @var oeVATTBEIncorrectVATArticlesMessageFormatter */
    private $_oVATTBEArticleMessageFormer;

    /** @var oxUtilsView */
    private $_oUtilsView;

    /**
     * Sets dependencies.
     *
     * @param oeVATTBEOrderArticleChecker                  $oVATTBEOrderArticleChecker  checks if article list has article with wrong TBE VAT.
     * @param oeVATTBEIncorrectVATArticlesMessageFormatter $oVATTBEArticleMessageFormer forms error message if article list has article with wrong TBE VAT.
     * @param oxUtilsView                                  $oUtilsView                  stores error message.
     */
    public function __construct($oVATTBEOrderArticleChecker, $oVATTBEArticleMessageFormer, $oUtilsView)
    {
        $this->_oVATTBEOrderArticleChecker = $oVATTBEOrderArticleChecker;
        $this->_oVATTBEArticleMessageFormer = $oVATTBEArticleMessageFormer;
        $this->_oUtilsView = $oUtilsView;
    }

    /**
     * Create instance of this object by creating dependencies.
     * Later should be replaced with DIC.
     *
     * @param oeVATTBEOxArticle $oBasketArticles basket article list to check if has article with wrong TBE VAT .
     *
     * @return oeVATTBEBasketItemsValidator
     */
    public static function createInstance($oBasketArticles)
    {
        $oTBEUser = oeVATTBETBEUser::createInstance();
        $oVATTBEOrderArticleChecker = oxNew('oeVATTBEOrderArticleChecker', $oBasketArticles, $oTBEUser);
        $oUtilsView = oxRegistry::get('oxUtilsView');
        $oVATTBEArticleMessageFormer = oxNew('oeVATTBEIncorrectVATArticlesMessageFormatter');

        $oVATTBEBasketItemsValidator = oxNew('oeVATTBEBasketItemsValidator', $oVATTBEOrderArticleChecker, $oVATTBEArticleMessageFormer, $oUtilsView);
        return $oVATTBEBasketItemsValidator;
    }

    /**
     * Check if there is article with wrong TBE VAT.
     * Form message and add it for displaying to oxUtilsView.
     *
     * @param string $sControllerWhereToShowMessage name of controller where to display and delete message.
     */
    public function validateTbeArticlesAndShowMessageIfNeeded($sControllerWhereToShowMessage)
    {
        $oVATTBEOrderArticleChecker = $this->_oVATTBEOrderArticleChecker;
        $blAllBasketArticlesValid = $oVATTBEOrderArticleChecker->isValid();
        if (!$blAllBasketArticlesValid) {
            $oInvalidArticles = $oVATTBEOrderArticleChecker->getInvalidArticles();
            $oEx = $this->_oVATTBEArticleMessageFormer->getMessage($oInvalidArticles);
            $this->_oUtilsView->addErrorToDisplay($oEx, false, true, '', $sControllerWhereToShowMessage);
        }
    }
}
