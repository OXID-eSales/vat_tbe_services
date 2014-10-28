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
 * Test class for oeVATTBEList.
 *
 * @covers oeVATTBEList
 */
class Unit_oeVATTBE_Models_oeVATTBEListTest extends OxidTestCase
{

    /**
     * Two items is set to list;
     * Should iterate through both items.
     */
    public function testIteratingThroughItems()
    {
        $oList = new oeVATTBEList(array(1, 2));

        $aElements = array();
        foreach ($oList as $iItem) {
            $aElements[] = $iItem;
        }

        $this->assertEquals(array(1, 2), $aElements);
    }

    /**
     * Empty list is iterated;
     * Iteration works correctly.
     */
    public function testIteratingThroughItemsWhenListIsEmpty()
    {
        $oList = new oeVATTBEList();

        $aElements = array();
        foreach ($oList as $iItem) {
            $aElements[] = $iItem;
        }

        $this->assertEquals(array(), $aElements);
    }

    /**
     * Two items is added to list;
     * Iterating through all items once;
     * Iterating through all items again;
     * Should iterate through both items.
     */
    public function testIteratingMultipleTimes()
    {
        $oList = new oeVATTBEList(array(1,2));

        foreach ($oList as $iItem) {
        }

        $aElements = array();
        foreach ($oList as $iItem) {
            $aElements[] = $iItem;
        }

        $this->assertEquals(array(1, 2), $aElements);
    }

    /**
     * Three items is added to list;
     * Correctly formed array is returned.
     */
    public function testReturningArray()
    {
        $oList = new oeVATTBEList(array(1,2));
        $oList->add(3);

        $this->assertEquals(array(1, 2, 3), $oList->getArray());
    }

    /**
     * Two items is added to empty list;
     * Both items should be correctly added to list;
     */
    public function testAdditionOfItemsToEmptyList()
    {
        $oList = new oeVATTBEList();
        $oList->add(1);
        $oList->add(2);

        $this->assertEquals(array(1, 2), $oList->getArray());
    }

    /**
     * Two items is added to non empty list;
     * Both items should be added to list without deleting existing items;
     */
    public function testAdditionOfItemsToNotEmptyList()
    {
        $oList = new oeVATTBEList(array(1,2));
        $oList->add(1);
        $oList->add(2);

        $this->assertEquals(array(1, 2, 1, 2), $oList->getArray());
    }

    /**
     * Two items is added to list;
     * Counting of items returns correct value.
     */
    public function testCountingOfListItems()
    {
        $oList = new oeVATTBEList(array(1,2));

        $this->assertEquals(2, count($oList));
    }
}
