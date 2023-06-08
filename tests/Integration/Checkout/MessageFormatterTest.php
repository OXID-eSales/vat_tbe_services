<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Checkout;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\IncorrectVATArticlesMessageFormatter;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Testing message formatter in IncorrectVATArticlesMessageFormatter.
 */
class MessageFormatterTest extends BaseTestCase
{

    /**
     * Provider for different article set to test if error message was formed correctly.
     *
     * @return array
     */
    public function providerGetMessage()
    {
        $oArticle1 = oxNew(Article::class);
        $oArticle1->assign([
            'oxtitle' => 'some article name'
        ]);

        $oArticle2 = oxNew(Article::class);
        $oArticle2->assign([
            'oxtitle' => 'some other name'
        ]);

        $oInvalidArticles1 = array($oArticle1);
        $oInvalidArticles2 = array($oArticle1, $oArticle2);

        $oLang = Registry::getLang();

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
        /** @var IncorrectVATArticlesMessageFormatter $oVATTBEArticleMessageFormer */
        $oVATTBEArticleMessageFormer = oxNew(IncorrectVATArticlesMessageFormatter::class);
        $sErrorMessage = $oVATTBEArticleMessageFormer->getMessage($oInvalidArticles);

        $this->assertSame($sExpectedMessage, $sErrorMessage->getOxMessage());
    }
}