<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Checkout;

use PHPUnit\Framework\TestCase;

/**
 * Testing message formatter in oeVATTBEIncorrectVATArticlesMessageFormatter.
 */
class MessageFormatterTest extends TestCase
{

    /**
     * Provider for different article set to test if error message was formed correctly.
     *
     * @return array
     */
    public function providerGetMessage()
    {
        $oArticle1 = oxNew('oxArticle');
        $oArticle1->oxarticles__oxtitle = new oxField('some article name', oxField::T_RAW);

        $oArticle2 = oxNew('oxArticle');
        $oArticle2->oxarticles__oxtitle = new oxField('some other name', oxField::T_RAW);

        $oInvalidArticles1 = array($oArticle1);
        $oInvalidArticles2 = array($oArticle1, $oArticle2);

        $oLang = oxRegistry::getLang();

        return array(
            array($oInvalidArticles1, sprintf($oLang->translateString('OEVATTBE_ERROR_MESSAGE_TBE_ARTICLE_VAT_PROBLEMS'), 'some article name')),
            array($oInvalidArticles2, sprintf($oLang->translateString('OEVATTBE_ERROR_MESSAGE_TBE_ARTICLE_VAT_PROBLEMS'), 'some article name, some other name')),
        );
    }

    /**
     * Test if error message is formed correctly.
     *
     * @param array  $oInvalidArticles fake articles to form error message.
     * @param string $sExpectedMessage expected error message.
     *
     * @dataProvider providerGetMessage
     */
    public function testGetMessage($oInvalidArticles, $sExpectedMessage)
    {
        /** @var oeVATTBEIncorrectVATArticlesMessageFormatter $oVATTBEArticleMessageFormer */
        $oVATTBEArticleMessageFormer = oxNew('oeVATTBEIncorrectVATArticlesMessageFormatter');
        $sErrorMessage = $oVATTBEArticleMessageFormer->getMessage($oInvalidArticles);

        $this->assertSame($sExpectedMessage, $sErrorMessage->getOxMessage());
    }
}