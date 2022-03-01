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
 * Testing VAT TBE administration in article page.
 *
 * @covers oeVATTBEArticleAdministration
 */
class Unit_oeVATTBE_controllers_oeVATTBEArticleAdministrationTest extends OxidTestCase
{
    /**
     * Test if render set readonly mode for subshops.
     *
     * @return true|null
     */
    public function testRenderSetsReadOnlyModeForSubshops()
    {
        if ('EE' != $this->getConfig()->getEdition()) {
            $this->markTestSkipped('Test only on Enterprise shop');
        }

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject oxArticle */
        $oDerivedArticle = $this->getMock('oxArticle', array(), array(), '', false);
        $oDerivedArticle->expects($this->any())->method('isDerived')->will($this->returnValue(true));

        /** @var oeVATTBEArticleAdministration|PHPUnit_Framework_MockObject_MockObject $oArticleAdministration */
        $oArticleAdministration = $this->getMock('oeVATTBEArticleAdministration', array('_loadCurrentArticle'));
        $oArticleAdministration->expects($this->atLeastOnce())->method('_loadCurrentArticle')->will($this->returnValue($oDerivedArticle));
        $oArticleAdministration->render();

        $aViewData = $oArticleAdministration->getViewData();
        $this->assertTrue($aViewData['readonly'], 'View data contains: '. serialize($aViewData));
    }

    /**
     * Test if render do not set readonly mode for main shop.
     *
     * @return true|null
     */
    public function testRenderDoNotSetsReadOnlyModeForMainShop()
    {
        if ('EE' != $this->getConfig()->getEdition()) {
            $this->markTestSkipped('Test only on Enterprise shop');
        }

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject oxArticle */
        $oDerivedArticle = $this->getMock('oxArticle', array(), array(), '', false);
        $oDerivedArticle->expects($this->any())->method('isDerived')->will($this->returnValue(false));

        /** @var oeVATTBEArticleAdministration|PHPUnit_Framework_MockObject_MockObject $oArticleAdministration */
        $oArticleAdministration = $this->getMock('oeVATTBEArticleAdministration', array('_loadCurrentArticle'));
        $oArticleAdministration->expects($this->atLeastOnce())->method('_loadCurrentArticle')->will($this->returnValue($oDerivedArticle));
        $oArticleAdministration->render();

        $aViewData = $oArticleAdministration->getViewData();
        $this->assertTrue(!isset($aViewData['readonly']), 'View data contains: '. serialize($aViewData));
    }

    /**
     * Test if render set readonly mode for subshops.
     *
     * @return true|null
     */
    public function testRenderDoesNotSetsReadOnlyModeForDifferentEditions()
    {
        if ('EE' == $this->getConfig()->getEdition()) {
            $this->markTestSkipped('Test only on Enterprise shop');
        }

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject oxArticle */
        $oDerivedArticle = $this->getMock('oxArticle', array(), array(), '', false);
        $oDerivedArticle->expects($this->any())->method('isDerived')->will($this->returnValue(true));

        /** @var oeVATTBEArticleAdministration|PHPUnit_Framework_MockObject_MockObject $oArticleAdministration */
        $oArticleAdministration = $this->getMock('oeVATTBEArticleAdministration', array('_loadCurrentArticle'));
        $oArticleAdministration->expects($this->atLeastOnce())->method('_loadCurrentArticle')->will($this->returnValue($oDerivedArticle));
        $oArticleAdministration->render();

        $aViewData = $oArticleAdministration->getViewData();
        $this->assertTrue(!isset($aViewData['readonly']), 'View data contains: '. serialize($aViewData));
    }
}
