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
 * Testing oeVATTBECategoryArticlesUpdater class.
 *
 * @covers oeVATTBECategoryVATGroupsPopulator
 */
class Unit_oeVatTbe_Models_oeVATTBECategoryArticlesUpdaterTest extends OxidTestCase
{
    /**
     * Tests creating of oeVATTBECategoryArticlesUpdater.
     */
    public function testCreating()
    {
        $oArticlesUpdater = oeVATTBECategoryArticlesUpdater::createInstance();

        $this->assertInstanceOf('oeVATTBECategoryArticlesUpdater', $oArticlesUpdater);
    }

    /**
     * Test deleting category groups list.
     */
    public function testDeletingCategoryVATGroupsList()
    {
        $oCategory = oxNew('oeVATTBEoxCategory');
        $oCategory->setId('categoryId');

        /** @var oeVATTBECategoryVATGroupsPopulatorDbGateway|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->getMock('oeVATTBECategoryVATGroupsPopulatorDbGateway', array('populate'));
        $oGateway->expects($this->once())->method('populate')->with('categoryId');

        /** @var oeVATTBECategoryArticlesUpdater $oList */
        $oArticlesUpdater = oxNew('oeVATTBECategoryArticlesUpdater', $oGateway);
        $oArticlesUpdater->populate($oCategory);
    }

    /**
     * Test if DB gateway method was called.
     */
    public function testResetArticles()
    {
        $aArticles = array(
            '_testId'
        );
        /** @var oeVATTBECategoryVATGroupsPopulatorDbGateway|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->getMock('oeVATTBECategoryVATGroupsPopulatorDbGateway', array('reset'));
        $oGateway->expects($this->once())->method('reset')->with($aArticles);

        /** @var oeVATTBECategoryArticlesUpdater $oList */
        $oArticlesUpdater = oxNew('oeVATTBECategoryArticlesUpdater', $oGateway);
        $oArticlesUpdater->reset($aArticles);
    }
}
