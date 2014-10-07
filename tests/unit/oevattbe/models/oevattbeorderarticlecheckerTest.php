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
 * Testing oeVATTBEOrderArticleChecker class.
 */
class Unit_oeVATTBE_models_oeVATTBEOrderArticleCheckerTest extends OxidTestCase
{
    public function testCheckingArticlesWithEmptyList()
    {
        $oChecker = oxNew('oeVATTBEOrderArticleChecker', array());

        $this->assertSame(true, $oChecker->isValid());
    }

    public function testCheckingArticlesWhenCorrectArticlesExists()
    {
        $oArticleWithoutVAT = $this->_createArticle(false, null);
        $oArticleWithVAT = $this->_createArticle(false, 15);
        $oTBEArticleWithVAT = $this->_createArticle(true, 15);

        $aArticles = array($oArticleWithoutVAT, $oArticleWithVAT, $oTBEArticleWithVAT);

        $oChecker = oxNew('oeVATTBEOrderArticleChecker', $aArticles);

        $this->assertSame(true, $oChecker->isValid());
    }

    public function testCheckingArticlesWhenIncorrectArticlesExists()
    {
        $oArticleWithoutVAT = $this->_createArticle(false, null);
        $oTBEArticleWithoutVAT = $this->_createArticle(true, null);

        $aArticles = array($oArticleWithoutVAT, $oTBEArticleWithoutVAT);

        $oChecker = oxNew('oeVATTBEOrderArticleChecker', $aArticles);

        $this->assertSame(false, $oChecker->isValid());

        return $oChecker;
    }

    public function testReturningInvalidArticlesWhenIncorrectArticlesExists()
    {
        $oArticleWithoutVAT = $this->_createArticle(false, null);
        $oTBEArticleWithoutVAT1 = $this->_createArticle(true, null);
        $oTBEArticleWithoutVAT2 = $this->_createArticle(true, null);

        $aArticles = array($oArticleWithoutVAT, $oTBEArticleWithoutVAT1, $oTBEArticleWithoutVAT2);

        $oChecker = oxNew('oeVATTBEOrderArticleChecker', $aArticles);

        $aIncorrectArticles = array($oTBEArticleWithoutVAT1, $oTBEArticleWithoutVAT2);

        $oChecker->isValid();

        $this->assertSame($aIncorrectArticles, $oChecker->getInvalidArticles());
    }

    protected function _createArticle($blTBEService, $iVat)
    {
        $oArticle = $this->getMock('oxArticle', array('isTBEService', 'getTBEVat'));
        $oArticle->expects($this->any())->method('isTBEService')->will($this->returnValue($blTBEService));
        $oArticle->expects($this->any())->method('getTBEVat')->will($this->returnValue($iVat));

        return $oArticle;
    }
}
