<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Checkout;

use OxidEsales\Eshop\Core\Field;
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
        // Return plain data only. Building models (oxNew) here would open a DB connection
        // at data-provider collection time, outside the test's setUp/transaction context.
        return [
            [['some article name'], 'some article name'],
            [['some article name', 'some other name'], 'some article name, some other name'],
        ];
    }

    /**
     * Test if error message is formed correctly.
     *
     * @param string[] $articleTitles titles of the fake articles to form the error message from.
     * @param string   $articleName   expected article name(s) in the error message.
     */
    #[DataProvider('providerGetMessage')]
    public function testGetMessage($articleTitles, $articleName)
    {
        $oInvalidArticles = [];
        foreach ($articleTitles as $sTitle) {
            // Set the title field directly rather than via assign(): assign() runs the
            // article's stock/status processing, which reads unset fields on a bare article
            // and emits shop-core PHP notices (floor(null) deprecation, "read property on
            // false" warnings). The formatter only needs oxtitle.
            $oArticle = oxNew(Article::class);
            $oArticle->oxarticles__oxtitle = new Field($sTitle);
            $oInvalidArticles[] = $oArticle;
        }

        /** @var IncorrectVATArticlesMessageFormatter $oVATTBEArticleMessageFormer */
        $oVATTBEArticleMessageFormer = oxNew(IncorrectVATArticlesMessageFormatter::class);
        $sErrorMessage = $oVATTBEArticleMessageFormer->getMessage($oInvalidArticles);

        $oLang = Registry::getLang();
        // phpcs:ignore Generic.Files.LineLength.TooLong
        $sExpectedMessage = sprintf($oLang->translateString('OEVATTBE_ERROR_MESSAGE_TBE_ARTICLE_VAT_PROBLEMS'), $articleName);

        $this->assertSame($sExpectedMessage, $sErrorMessage->getOxMessage());
    }
}
