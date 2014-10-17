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
 * Testing message formatter in oeVATTBEIncorrectVATArticlesMessageFormatter.
 */
class Integration_oeVatTbe_checkout_oeVATTBEMessageFormatterTest extends OxidTestCase
{

    /**
     * Provider for different article set to test if error message was formed correctly.
     *
     * @return array
     */
    public function providerGetMessage()
    {
        $oArticle1 = oxNew('oeVATTBEOxArticle');
        $oArticle1->oxarticles__oxtitle = new oxField('some article name', oxField::T_RAW);

        $oArticle2 = oxNew('oeVATTBEOxArticle');
        $oArticle2->oxarticles__oxtitle = new oxField('some other name', oxField::T_RAW);

        $oInvalidArticles1 = array($oArticle1);
        $oInvalidArticles2 = array($oArticle1, $oArticle2);

        return array(
            array($oInvalidArticles1, '[tr]Some articles can not be sold because of VAT problems in your country: some article name'),
            array($oInvalidArticles2, '[tr]Some articles can not be sold because of VAT problems in your country: some article name, some other name'),
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