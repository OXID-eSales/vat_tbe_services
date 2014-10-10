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
 * Testing extended oxArticle class.
 */
class Unit_oeVATTBE_models_oeVATTBEOxBasketContentMarkGeneratorTest extends OxidTestCase
{
    public function testGetMark()
    {
        $oBasket = $this->getMock('oxBasket', array('hasVATTBEArticles'));
        $oBasket->expects($this->any())->method('hasVATTBEArticles')->will($this->returnValue(true));

        $oGenerator = oxNew('oxBasketContentMarkGenerator', $oBasket);

        $this->assertSame('**', $oGenerator->getMark('tbeService'));
    }

    public function testGetMarkHasOtherMarks()
    {
        $oBasket = $this->getMock('oxBasket', array('hasVATTBEArticles', 'hasArticlesWithDownloadableAgreement'));
        $oBasket->expects($this->any())->method('hasVATTBEArticles')->will($this->returnValue(true));
        $oBasket->expects($this->any())->method('hasArticlesWithDownloadableAgreement')->will($this->returnValue(true));

        $oGenerator = oxNew('oxBasketContentMarkGenerator', $oBasket);

        $this->assertSame('**', $oGenerator->getMark('tbeService'));
        $this->assertSame('***', $oGenerator->getMark('downloadable'));
    }

    public function testGetMarkHasOtherMarksButNotTBE()
    {
        $oBasket = $this->getMock('oxBasket', array('hasVATTBEArticles', 'hasArticlesWithDownloadableAgreement'));
        $oBasket->expects($this->any())->method('hasVATTBEArticles')->will($this->returnValue(false));
        $oBasket->expects($this->any())->method('hasArticlesWithDownloadableAgreement')->will($this->returnValue(true));

        $oGenerator = oxNew('oxBasketContentMarkGenerator', $oBasket);

        $this->assertSame(null, $oGenerator->getMark('tbeService'));
        $this->assertSame('**', $oGenerator->getMark('downloadable'));
    }
}
