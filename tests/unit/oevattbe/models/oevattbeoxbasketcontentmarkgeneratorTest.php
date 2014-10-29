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
 * Testing extended oxArticle class.
 *
 * @covers oeVATTBEOxBasketContentMarkGenerator
 */
class Unit_oeVATTBE_models_oeVATTBEOxBasketContentMarkGeneratorTest extends OxidTestCase
{
    /**
     * Mark calculation test case: just tbe
     */
    public function testGetMark()
    {
        $oBasket = $this->getMock('oeVATTBEOxBasket', array('hasOeTBEVATArticles'));
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));

        $oGenerator = oxNew('oxBasketContentMarkGenerator', $oBasket);

        $this->assertSame('**', $oGenerator->getMark('tbeService'));
    }

    /**
     * Mark calculation test case: tbe and other
     */
    public function testGetMarkHasOtherMarks()
    {
        $oBasket = $this->getMock('oeVATTBEOxBasket', array('hasOeTBEVATArticles', 'hasArticlesWithDownloadableAgreement'));
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(true));
        $oBasket->expects($this->any())->method('hasArticlesWithDownloadableAgreement')->will($this->returnValue(true));

        $oGenerator = oxNew('oxBasketContentMarkGenerator', $oBasket);

        $this->assertSame('**', $oGenerator->getMark('tbeService'));
        $this->assertSame('***', $oGenerator->getMark('downloadable'));
    }

    /**
     * Mark calculation test case: no tbe
     */
    public function testGetMarkHasOtherMarksButNotTBE()
    {
        $oBasket = $this->getMock('oeVATTBEOxBasket', array('hasOeTBEVATArticles', 'hasArticlesWithDownloadableAgreement'));
        $oBasket->expects($this->any())->method('hasOeTBEVATArticles')->will($this->returnValue(false));
        $oBasket->expects($this->any())->method('hasArticlesWithDownloadableAgreement')->will($this->returnValue(true));

        $oGenerator = oxNew('oxBasketContentMarkGenerator', $oBasket);

        $this->assertSame(null, $oGenerator->getMark('tbeService'));
        $this->assertSame('**', $oGenerator->getMark('downloadable'));
    }
}
