<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\VisualCmsModule\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;

/**
 * Testing OrderArticleChecker class.
 */
class OrderArticleCheckerTest extends TestCase
{
    /**
     * Provider for test.
     *
     * @return array
     */
    public function providerCheckingArticlesWithEmptyList()
    {
        $oCountry = $this->getMock(\OxidEsales\Eshop\Application\Model\Country::class, array('isInEU', 'appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getCountry'), array(), '', false);
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
        $oChecker = oxNew(oeVATTBEOrderArticleChecker::class, $mEmptyList, $oUser);

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

        $oCountry = $this->getMock(\OxidEsales\Eshop\Application\Model\Country::class, array('isInEU', 'appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getCountry'), array(), '', false);
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

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getCountry'), array(), '', false);
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

        $oCountry = $this->getMock(\OxidEsales\Eshop\Application\Model\Country::class, array('isInEU', 'appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getCountry'), array(), '', false);
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

        $oCountry = $this->getMock(\OxidEsales\Eshop\Application\Model\Country::class, array('isInEU', 'appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getCountry'), array(), '', false);
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

        $oCountry = $this->getMock(\OxidEsales\Eshop\Application\Model\Country::class, array('isInEU', 'appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(false));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(true));

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getCountry'), array(), '', false);
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

        $oCountry = $this->getMock(\OxidEsales\Eshop\Application\Model\Country::class, array('isInEU', 'appliesOeTBEVATTbeVat'));
        $oCountry->expects($this->any())->method('isInEU')->will($this->returnValue(true));
        $oCountry->expects($this->any())->method('appliesOeTBEVATTbeVat')->will($this->returnValue(false));

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getCountry'), array(), '', false);
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
