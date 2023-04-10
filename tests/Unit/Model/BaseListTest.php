<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\VisualCmsModule\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;

/**
 * Test class for oeVATTBEList.
 *
 * @covers BaseList
 */
class BaseListTest extends TestCase
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
