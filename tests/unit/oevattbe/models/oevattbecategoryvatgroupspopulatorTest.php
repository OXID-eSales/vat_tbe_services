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
 * Testing oeVATTBECategoryVATGroupsPopulator class.
 *
 * @covers oeVATTBECategoryVATGroupsPopulator
 */
class Unit_oeVatTbe_Models_oeVATTBECategoryVATGroupsPopulatorTest extends OxidTestCase
{
    /**
     * Tests creating of oeVATTBECategoryVATGroupsPopulator.
     */
    public function testCreating()
    {
        $oPopulator = oeVATTBECategoryVATGroupsPopulator::createInstance();

        $this->assertInstanceOf('oeVATTBECategoryVATGroupsPopulator', $oPopulator);
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

        /** @var oeVATTBECategoryVATGroupsPopulator $oList */
        $oPopulator = oxNew('oeVATTBECategoryVATGroupsPopulator', $oGateway);
        $oPopulator->populate($oCategory);
    }
}
