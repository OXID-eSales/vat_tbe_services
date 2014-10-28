<?php
/**
 * This file is part of OXID eSales eVAT module.
 *
 * OXID eSales eVAT module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales eVAT module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales eVAT module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
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
