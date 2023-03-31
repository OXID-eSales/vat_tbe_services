<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Service;

use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\EVatModule\Model\IncorrectVATArticlesMessageFormatter;
use OxidEsales\EVatModule\Model\OrderArticleChecker;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * Store logic how to find articles with wrong TBE VAT.
 */
class BasketItemsValidator
{
    use ServiceContainer;

    /**
     * Sets dependencies.
     *
     * @param OrderArticleChecker                  $orderArticleChecker  checks if article list has article with wrong TBE VAT.
     * @param IncorrectVATArticlesMessageFormatter $incorrectVATArticlesMessageFormatter forms error message if article list has article with wrong TBE VAT.
     * @param UtilsView                            $utilsView                  stores error message.
     */
    public function __construct(
        private OrderArticleChecker $orderArticleChecker,
        private IncorrectVATArticlesMessageFormatter $incorrectVATArticlesMessageFormatter,
        private UtilsView $utilsView
    ) {
    }

    /**
     * Check if there is article with wrong TBE VAT.
     * Form message and add it for displaying to oxUtilsView.
     *
     * @param string $sControllerWhereToShowMessage name of controller where to display and delete message.
     */
    public function validateTbeArticlesAndShowMessageIfNeeded($sControllerWhereToShowMessage)
    {
        $oVATTBEOrderArticleChecker = $this->orderArticleChecker;
        $blAllBasketArticlesValid = $oVATTBEOrderArticleChecker->isValid();
        if (!$blAllBasketArticlesValid) {
            $oInvalidArticles = $oVATTBEOrderArticleChecker->getInvalidArticles();
            $oEx = $this->incorrectVATArticlesMessageFormatter->getMessage($oInvalidArticles);
            $this->utilsView->addErrorToDisplay($oEx, false, true, '', $sControllerWhereToShowMessage);
        }
    }
}
