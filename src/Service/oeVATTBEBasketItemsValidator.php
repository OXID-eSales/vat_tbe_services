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

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\EVatModule\Model\oeVATTBEIncorrectVATArticlesMessageFormatter;
use OxidEsales\EVatModule\Model\oeVATTBEOrderArticleChecker;
use OxidEsales\EVatModule\Model\oeVATTBETBEUser;
use OxidEsales\EVatModule\Shop\oeVATTBEOxArticle;

/**
 * Store logic how to find articles with wrong TBE VAT.
 */
class oeVATTBEBasketItemsValidator
{
    /** @var oeVATTBEOrderArticleChecker */
    private $_oVATTBEOrderArticleChecker;

    /** @var oeVATTBEIncorrectVATArticlesMessageFormatter */
    private $_oVATTBEArticleMessageFormer;

    /** @var UtilsView */
    private $_oUtilsView;

    /**
     * Sets dependencies.
     *
     * @param oeVATTBEOrderArticleChecker                  $oVATTBEOrderArticleChecker  checks if article list has article with wrong TBE VAT.
     * @param oeVATTBEIncorrectVATArticlesMessageFormatter $oVATTBEArticleMessageFormer forms error message if article list has article with wrong TBE VAT.
     * @param UtilsView                                  $oUtilsView                  stores error message.
     */
    public function __construct(
        oeVATTBEOrderArticleChecker $oVATTBEOrderArticleChecker,
        oeVATTBEIncorrectVATArticlesMessageFormatter $oVATTBEArticleMessageFormer,
        UtilsView $oUtilsView
    ) {
        $this->_oVATTBEOrderArticleChecker = $oVATTBEOrderArticleChecker;
        $this->_oVATTBEArticleMessageFormer = $oVATTBEArticleMessageFormer;
        $this->_oUtilsView = $oUtilsView;
    }

    /**
     * Create instance of this object by creating dependencies.
     * Later should be replaced with DIC.
     *
     * @param array $oBasketArticles basket article list to check if has article with wrong TBE VAT .
     *
     * @return oeVATTBEBasketItemsValidator
     */
    public static function createInstance($oBasketArticles)
    {
        $oTBEUser = oeVATTBETBEUser::createInstance();
        $oVATTBEOrderArticleChecker = oxNew(oeVATTBEOrderArticleChecker::class, $oBasketArticles, $oTBEUser);
        $oUtilsView = Registry::getUtilsView();
        $oVATTBEArticleMessageFormer = oxNew(oeVATTBEIncorrectVATArticlesMessageFormatter::class);

        $oVATTBEBasketItemsValidator = oxNew(oeVATTBEBasketItemsValidator::class, $oVATTBEOrderArticleChecker, $oVATTBEArticleMessageFormer, $oUtilsView);
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
