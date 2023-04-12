<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\EVatModule\Model\IncorrectVATArticlesMessageFormatter;
use OxidEsales\EVatModule\Shop\Article;
use PHPUnit\Framework\TestCase;

/**
 * Test class for oeVATTBEArticleMessageFormatter.
 *
 * @covers IncorrectVATArticlesMessageFormatter
 */
class IncorrectVATArticlesMessageFormatterTest extends TestCase
{
    /**
     * Test if error message is formed correctly.
     */
    public function testGetMessage()
    {
        $oArticle = oxNew(Article::class);
        $oArticle->oxarticles__oxtitle = new Field('some other name', Field::T_RAW);

        /** @var IncorrectVATArticlesMessageFormatter $oVATTBEArticleMessageFormer */
        $oVATTBEArticleMessageFormer = oxNew(IncorrectVATArticlesMessageFormatter::class);
        $oErrorMessage = $oVATTBEArticleMessageFormer->getMessage([$oArticle]);

        $this->assertInstanceOf('oxDisplayError', $oErrorMessage);
    }
}
