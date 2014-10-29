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
 * Testing oeVATTBEOrderArticleChecker class.
 */
class Unit_oeVATTBE_models_oeVATTBEOrderArticleCheckerTest extends OxidTestCase
{
    /**
     * Provider for test.
     *
     * @return array
     */
    public function providerCheckingArticlesWithEmptyList()
    {
        $oCountry = $this->getMock('oeVATTBEOxCountry', array('isInEU', 'appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

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
     * Checks articles with empty list.
     *
     * @param array|null      $mEmptyList article list
     * @param oeVATTBETBEUser $oUser      user
     *
     * @dataProvider providerCheckingArticlesWithEmptyList
     */
    public function testCheckingArticlesWithEmptyList($mEmptyList, $oUser)
    {
        $oChecker = oxNew('oeVATTBEOrderArticleChecker', $mEmptyList, $oUser);

        $this->assertSame(true, $oChecker->isValid());
    }

    /**
     * Checks articles if valid.
     */
    public function testCheckingArticlesWhenCorrectArticlesExists()
    {
        $oArticleWithoutVAT = $this->_createArticle(false, null);
        $oArticleWithVAT = $this->_createArticle(false, 15);
        $oTBEArticleWithVAT = $this->_createArticle(true, 15);
        $oTBEArticleWithZeroVAT = $this->_createArticle(true, 0);

        $aArticles = array($oArticleWithoutVAT, $oArticleWithVAT, $oTBEArticleWithVAT, $oTBEArticleWithZeroVAT);

        $oCountry = $this->getMock('oeVATTBEOxCountry', array('isInEU', 'appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->getMock('oeVATTBETBEUser', array('getCountry'), array(), '', false);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = oxNew('oeVATTBEOrderArticleChecker', $aArticles, $oUser);

        $this->assertTrue($oChecker->isValid());
    }

    /**
     * Checks articles if not valid.
     */
    public function testCheckingArticlesWhenCorrectArticlesExistsButCountryNot()
    {
        $oArticleWithoutVAT = $this->_createArticle(false, null);
        $oArticleWithVAT = $this->_createArticle(false, 15);
        $oTBEArticleWithVAT = $this->_createArticle(true, 15);
        $oTBEArticleWithZeroVAT = $this->_createArticle(true, 0);

        $aArticles = array($oArticleWithoutVAT, $oArticleWithVAT, $oTBEArticleWithVAT, $oTBEArticleWithZeroVAT);

        $oUser = $this->getMock('oeVATTBETBEUser', array('getCountry'), array(), '', false);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue(null));

        $oChecker = oxNew('oeVATTBEOrderArticleChecker', $aArticles, $oUser);

        $this->assertFalse($oChecker->isValid());
    }

    /**
     * Checks articles if not valid.
     *
     * @return oeVATTBEOrderArticleChecker
     */
    public function testCheckingArticlesWhenIncorrectArticlesExists()
    {
        $oArticleWithoutVAT = $this->_createArticle(false, null);
        $oTBEArticleWithoutVAT = $this->_createArticle(true, null);

        $aArticles = array($oArticleWithoutVAT, $oTBEArticleWithoutVAT);

        $oCountry = $this->getMock('oeVATTBEOxCountry', array('isInEU', 'appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->getMock('oeVATTBETBEUser', array('getCountry'), array(), '', false);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = oxNew('oeVATTBEOrderArticleChecker', $aArticles, $oUser);

        $this->assertFalse($oChecker->isValid());

        return $oChecker;
    }

    /**
     * Checks invalid articles.
     */
    public function testReturningInvalidArticlesWhenIncorrectArticlesExists()
    {
        $oArticleWithoutVAT = $this->_createArticle(false, null, 'id');
        $oTBEArticleWithoutVAT1 = $this->_createArticle(true, null, 'id1');
        $oTBEArticleWithoutVAT2 = $this->_createArticle(true, null, 'id2');

        $aArticles = array($oArticleWithoutVAT, $oTBEArticleWithoutVAT1, $oTBEArticleWithoutVAT2);

        $oCountry = $this->getMock('oeVATTBEOxCountry', array('isInEU', 'appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->getMock('oeVATTBETBEUser', array('getCountry'), array(), '', false);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = oxNew('oeVATTBEOrderArticleChecker', $aArticles, $oUser);

        $aIncorrectArticles = array('id1'=>$oTBEArticleWithoutVAT1, 'id2'=>$oTBEArticleWithoutVAT2);

        $this->assertSame($aIncorrectArticles, $oChecker->getInvalidArticles());
    }

    /**
     * Checks articles if valid.
     *
     * @return oeVATTBEOrderArticleChecker
     */
    public function testCheckingArticlesWhenIncorrectArticlesExistsButCountryIsNotEu()
    {
        $oArticleWithoutVAT = $this->_createArticle(false, null);
        $oTBEArticleWithoutVAT = $this->_createArticle(true, null);

        $aArticles = array($oArticleWithoutVAT, $oTBEArticleWithoutVAT);

        $oCountry = $this->getMock('oeVATTBEOxCountry', array('isInEU', 'appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(false));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->getMock('oeVATTBETBEUser', array('getCountry'), array(), '', false);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = oxNew('oeVATTBEOrderArticleChecker', $aArticles, $oUser);

        $this->assertTrue($oChecker->isValid());

        return $oChecker;
    }

    /**
     * Checks articles if valid.
     *
     * @return oeVATTBEOrderArticleChecker
     */
    public function testCheckingArticlesWhenIncorrectArticlesExistsButCountryIsEuButNotTBE()
    {
        $oArticleWithoutVAT = $this->_createArticle(false, null);
        $oTBEArticleWithoutVAT = $this->_createArticle(true, null);

        $aArticles = array($oArticleWithoutVAT, $oTBEArticleWithoutVAT);

        $oCountry = $this->getMock('oeVATTBEOxCountry', array('isInEU', 'appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(false));

        $oUser = $this->getMock('oeVATTBETBEUser', array('getCountry'), array(), '', false);
        $oUser->expects($this->any())->method('getCountry')->will($this->returnValue($oCountry));

        $oChecker = oxNew('oeVATTBEOrderArticleChecker', $aArticles, $oUser);

        $this->assertTrue($oChecker->isValid());

        return $oChecker;
    }

    /**
     * Creates article.
     *
     * @param bool   $blTBEService is article tbe service or not
     * @param int    $iVat         VAT rate
     * @param string $sId          article id
     *
     * @return oxArticle|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _createArticle($blTBEService, $iVat, $sId = null)
    {
        $oArticle = $this->getMock('oxArticle', array('isOeVATTBETBEService', 'getOeVATTBETBEVat', 'getId'));
        $oArticle->expects($this->any())->method('isOeVATTBETBEService')->will($this->returnValue($blTBEService));
        $oArticle->expects($this->any())->method('getOeVATTBETBEVat')->will($this->returnValue($iVat));
        if (!is_null($sId)) {
            $oArticle->expects($this->any())->method('getId')->will($this->returnValue($sId));
        }

        return $oArticle;
    }
}
