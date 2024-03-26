<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Model;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\EVatModule\Model\IncorrectVATArticlesMessageFormatter;
use OxidEsales\EVatModule\Shop\Article;
use PHPUnit\Framework\TestCase;

/**
 * Test class for oeVATTBEArticleMessageFormatter.
 */
class IncorrectVATArticlesMessageFormatterTest extends TestCase
{
    /**
     * Test if error message is formed correctly.
     */
    public function testGetMessage()
    {
        $oArticle = oxNew(Article::class);
        $oArticle->assign([
            'oxtitle' => 'some other name',
            'oxarticles__oxstock' => 1,
            'oxshopid'    => 1,
            'oxstockflag' => 1,
            'oxstock'     => 999,
            'oxvarstock'  => 999,
            'oxvarcount'  => 999,
        ]);
        $oArticle->save();

        /** @var IncorrectVATArticlesMessageFormatter $oVATTBEArticleMessageFormer */
        $oVATTBEArticleMessageFormer = oxNew(IncorrectVATArticlesMessageFormatter::class);
        $oErrorMessage = $oVATTBEArticleMessageFormer->getMessage([$oArticle]);

        $this->assertInstanceOf('oxDisplayError', $oErrorMessage);
    }
}
