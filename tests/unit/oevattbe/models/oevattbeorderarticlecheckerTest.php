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

    public function providerCheckingArticlesWithEmptyList()
    {
        $oCountry = $this->getMock('oxCountry', array('isInEU', 'appliesTBEVAT'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesTBEVAT')->will($this->returnValue(true));

        $oUser = $this->getMock('oeVATTBETBEUser', array('getCountry'), array(), '', false);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        return array(
            array(array(), $oUser),
            array('', $oUser),
            array(null, $oUser),
            array(false, $oUser),
        );
    }

    /**
     * @dataProvider providerCheckingArticlesWithEmptyList
     */
    public function testCheckingArticlesWithEmptyList($mEmptyList, $oUser)
    {
        $oChecker = oxNew('oeVATTBEOrderArticleChecker', $mEmptyList, $oUser);

        $this->assertSame(true, $oChecker->isValid());
    }

    public function testCheckingArticlesWhenCorrectArticlesExists()
    {
        $oArticleWithoutVAT = $this->_createArticle(false, null);
        $oArticleWithVAT = $this->_createArticle(false, 15);
        $oTBEArticleWithVAT = $this->_createArticle(true, 15);
        $oTBEArticleWithZeroVAT = $this->_createArticle(true, 0);

        $aArticles = array($oArticleWithoutVAT, $oArticleWithVAT, $oTBEArticleWithVAT, $oTBEArticleWithZeroVAT);

        $oCountry = $this->getMock('oxCountry', array('isInEU', 'appliesTBEVAT'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesTBEVAT')->will($this->returnValue(true));

        $oUser = $this->getMock('oeVATTBETBEUser', array('getCountry'), array(), '', false);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = oxNew('oeVATTBEOrderArticleChecker', $aArticles, $oUser);

        $this->assertTrue($oChecker->isValid());
    }

    public function testCheckingArticlesWhenIncorrectArticlesExists()
    {
        $oArticleWithoutVAT = $this->_createArticle(false, null);
        $oTBEArticleWithoutVAT = $this->_createArticle(true, null);

        $aArticles = array($oArticleWithoutVAT, $oTBEArticleWithoutVAT);

        $oCountry = $this->getMock('oxCountry', array('isInEU', 'appliesTBEVAT'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesTBEVAT')->will($this->returnValue(true));

        $oUser = $this->getMock('oeVATTBETBEUser', array('getCountry'), array(), '', false);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = oxNew('oeVATTBEOrderArticleChecker', $aArticles, $oUser);

        $this->assertFalse($oChecker->isValid());

        return $oChecker;
    }

    public function testReturningInvalidArticlesWhenIncorrectArticlesExists()
    {
        $oArticleWithoutVAT = $this->_createArticle(false, null);
        $oTBEArticleWithoutVAT1 = $this->_createArticle(true, null);
        $oTBEArticleWithoutVAT2 = $this->_createArticle(true, null);

        $aArticles = array($oArticleWithoutVAT, $oTBEArticleWithoutVAT1, $oTBEArticleWithoutVAT2);

        $oCountry = $this->getMock('oxCountry', array('isInEU', 'appliesTBEVAT'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesTBEVAT')->will($this->returnValue(true));

        $oUser = $this->getMock('oeVATTBETBEUser', array('getCountry'), array(), '', false);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = oxNew('oeVATTBEOrderArticleChecker', $aArticles, $oUser);

        $aIncorrectArticles = array($oTBEArticleWithoutVAT1, $oTBEArticleWithoutVAT2);

        $this->assertSame($aIncorrectArticles, $oChecker->getInvalidArticles());
    }


    public function testCheckingArticlesWhenIncorrectArticlesExistsButCountryIsNotEu()
    {
        $oArticleWithoutVAT = $this->_createArticle(false, null);
        $oTBEArticleWithoutVAT = $this->_createArticle(true, null);

        $aArticles = array($oArticleWithoutVAT, $oTBEArticleWithoutVAT);

        $oCountry = $this->getMock('oxCountry', array('isInEU', 'appliesTBEVAT'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(false));
        $oCountry->expects($this->any())->method('appliesTBEVAT')->will($this->returnValue(true));

        $oUser = $this->getMock('oeVATTBETBEUser', array('getCountry'), array(), '', false);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = oxNew('oeVATTBEOrderArticleChecker', $aArticles, $oUser);

        $this->assertTrue($oChecker->isValid());

        return $oChecker;
    }

    public function testCheckingArticlesWhenIncorrectArticlesExistsButCountryIsEuButNotTBE()
    {
        $oArticleWithoutVAT = $this->_createArticle(false, null);
        $oTBEArticleWithoutVAT = $this->_createArticle(true, null);

        $aArticles = array($oArticleWithoutVAT, $oTBEArticleWithoutVAT);

        $oCountry = $this->getMock('oxCountry', array('isInEU', 'appliesTBEVAT'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesTBEVAT')->will($this->returnValue(false));

        $oUser = $this->getMock('oeVATTBETBEUser', array('getCountry'), array(), '', false);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = oxNew('oeVATTBEOrderArticleChecker', $aArticles, $oUser);

        $this->assertTrue($oChecker->isValid());

        return $oChecker;
    }

    protected function _createArticle($blTBEService, $iVat)
    {
        $oArticle = $this->getMock('oxArticle', array('isTBEService', 'getTBEVat'));
        $oArticle->expects($this->any())->method('isTBEService')->will($this->returnValue($blTBEService));
        $oArticle->expects($this->any())->method('getTBEVat')->will($this->returnValue($iVat));

        return $oArticle;
    }
}
