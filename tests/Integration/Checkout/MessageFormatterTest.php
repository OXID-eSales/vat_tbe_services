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
use PHPUnit\Framework\Attributes\DataProvider;

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
    public static function providerGetMessage()
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

        return array(
            array($oInvalidArticles1, 'some article name'),
            array($oInvalidArticles2, 'some article name, some other name'),
        );
    }

    /**
     * Test if error message is formed correctly.
     *
     * @param array  $oInvalidArticles fake articles to form error message.
     * @param string $sExpectedMessage expected error message.
     */
    #[DataProvider('providerGetMessage')]
    public function testGetMessage($oInvalidArticles, $articleName)
    {
        /** @var IncorrectVATArticlesMessageFormatter $oVATTBEArticleMessageFormer */
        $oVATTBEArticleMessageFormer = oxNew(IncorrectVATArticlesMessageFormatter::class);
        $sErrorMessage = $oVATTBEArticleMessageFormer->getMessage($oInvalidArticles);

        $oLang = Registry::getLang();
        $sExpectedMessage = sprintf($oLang->translateString('OEVATTBE_ERROR_MESSAGE_TBE_ARTICLE_VAT_PROBLEMS'), $articleName);

        $this->assertSame($sExpectedMessage, $sErrorMessage->getOxMessage());
    }
}