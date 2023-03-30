<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Testing TBEUser class.
 *
 * @covers oeVATTBEBasketItemsValidator
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

        $oVATTBEArticleMessageFormer = $this->getMock('oeVATTBEIncorrectVATArticlesMessageFormatter');

        $oUtilsView = $this->getMock('oxUtilsView');
        // Error message should not be set as oeVATTBEOrderArticleChecker indicates that there is no wrong article.
        $oUtilsView->expects($this->never())->method('addErrorToDisplay');

        /** @var oeVATTBEBasketItemsValidator $oVATTBEBasketItemsValidator */
        $oVATTBEBasketItemsValidator = oxNew('oeVATTBEBasketItemsValidator', $oVATTBEOrderArticleChecker, $oVATTBEArticleMessageFormer, $oUtilsView);
        $oVATTBEBasketItemsValidator->validateTbeArticlesAndShowMessageIfNeeded('basket');
    }

    /**
     * Test that error message set when there are articles with wrong TBE VAT.
     */
    public function testValidateTbeArticlesWhenIncorrectArticleExist()
    {
        $oVATTBEOrderArticleChecker = $this->getMock('oeVATTBEOrderArticleChecker', array(), array(), '', false);
        $oVATTBEOrderArticleChecker->expects($this->any())->method('isValid')->will($this->returnValue(false));

        $oVATTBEArticleMessageFormer = $this->getMock('oeVATTBEIncorrectVATArticlesMessageFormatter');

        $oUtilsView = $this->getMock('oxUtilsView');
        // Error message should be set as oeVATTBEOrderArticleChecker indicates that there is wrong article.
        $oUtilsView->expects($this->atLeastOnce())->method('addErrorToDisplay');

        /** @var oeVATTBEBasketItemsValidator $oVATTBEBasketItemsValidator */
        $oVATTBEBasketItemsValidator = oxNew('oeVATTBEBasketItemsValidator', $oVATTBEOrderArticleChecker, $oVATTBEArticleMessageFormer, $oUtilsView);
        $oVATTBEBasketItemsValidator->validateTbeArticlesAndShowMessageIfNeeded('basket');
    }
}
