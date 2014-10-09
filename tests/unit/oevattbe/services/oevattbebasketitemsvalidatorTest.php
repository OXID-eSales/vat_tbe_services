<?php
/**
 * This file is part of OXID eSales VAT TBE module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales VAT TBE module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */


/**
 * Testing TBEUser class.
 */
class Unit_oeVatTbe_services_oeVATTBEBasketItemsValidatorTest extends OxidTestCase
{
    /**
     * Test if no error message set when there are no articles with wrong TBE VAT.
     */
    public function testValidateTbeArticlesWhenAllArticlesCorrect()
    {
        $oVATTBEOrderArticleChecker = $this->getMock('oeVATTBEOrderArticleChecker', array(), array(), '', false);
        $oVATTBEOrderArticleChecker->expects($this->any())->method('isValid')->will($this->returnValue(true));

        $oVATTBEArticleMessageFormer = $this->getMock('oeVATTBEArticleMessageFormatter');

        $oUtilsView = $this->getMock('oxUtilsView');
        // Error message should not be set as oeVATTBEOrderArticleChecker indicates that there is no wrong article.
        $oUtilsView->expects($this->never())->method('addErrorToDisplay');

        /** @var oeVATTBEBasketItemsValidator $oVATTBEBasketItemsValidator */
        $oVATTBEBasketItemsValidator = oxNew('oeVATTBEBasketItemsValidator', $oVATTBEOrderArticleChecker, $oVATTBEArticleMessageFormer, $oUtilsView);
        $oVATTBEBasketItemsValidator->validateTbeArticlesAndShowMessageIfNeeded();
    }
    /**
     * Test that error message set when there are articles with wrong TBE VAT.
     */
    public function testValidateTbeArticlesWhenIncorrectArticleExist()
    {
        $oVATTBEOrderArticleChecker = $this->getMock('oeVATTBEOrderArticleChecker', array(), array(), '', false);
        $oVATTBEOrderArticleChecker->expects($this->any())->method('isValid')->will($this->returnValue(false));

        $oVATTBEArticleMessageFormer = $this->getMock('oeVATTBEArticleMessageFormatter');

        $oUtilsView = $this->getMock('oxUtilsView');
        // Error message should be set as oeVATTBEOrderArticleChecker indicates that there is wrong article.
        $oUtilsView->expects($this->atLeastOnce())->method('addErrorToDisplay');

        /** @var oeVATTBEBasketItemsValidator $oVATTBEBasketItemsValidator */
        $oVATTBEBasketItemsValidator = oxNew('oeVATTBEBasketItemsValidator', $oVATTBEOrderArticleChecker, $oVATTBEArticleMessageFormer, $oUtilsView);
        $oVATTBEBasketItemsValidator->validateTbeArticlesAndShowMessageIfNeeded();
    }
}
