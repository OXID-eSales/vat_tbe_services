<?php
/**
 * This file is part of OXID eSales eVAT module.
 *
 * OXID eSales eVAT module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales eVAT module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales eVAT module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
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
