<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Test class for oeVATTBEArticleMessageFormatter.
 *
 * @covers oeVATTBEIncorrectVATArticlesMessageFormatter
 */
class Unit_oeVATTBE_Models_oeVATTBEIncorrectVATArticlesMessageFormatterTest extends OxidTestCase
{
    /**
     * Test if error message is formed correctly.
     */
    public function testGetMessage()
    {
        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxtitle = new oxField('some other name', oxField::T_RAW);

        /** @var oeVATTBEIncorrectVATArticlesMessageFormatter $oVATTBEArticleMessageFormer */
        $oVATTBEArticleMessageFormer = oxNew('oeVATTBEIncorrectVATArticlesMessageFormatter');
        $oErrorMessage = $oVATTBEArticleMessageFormer->getMessage(array($oArticle));

        $this->assertInstanceOf('oxDisplayError', $oErrorMessage);
    }
}
