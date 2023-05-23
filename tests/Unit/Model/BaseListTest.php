<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model;

use OxidEsales\EVatModule\Model\BaseList;
use PHPUnit\Framework\TestCase;

/**
 * Test class for BaseList.
 */
class BaseListTest extends TestCase
{
    /**
     * Two items is set to list;
     * Should iterate through both items.
     */
    public function testIteratingThroughItems()
    {
        $oList = new BaseList([1, 2]);

        $aElements = [];
        foreach ($oList as $iItem) {
            $aElements[] = $iItem;
        }

        $this->assertEquals([1, 2], $aElements);
    }

    /**
     * Empty list is iterated;
     * Iteration works correctly.
     */
    public function testIteratingThroughItemsWhenListIsEmpty()
    {
        $oList = new BaseList();

        $aElements = [];
        foreach ($oList as $iItem) {
            $aElements[] = $iItem;
        }

        $this->assertEquals([], $aElements);
    }

    /**
     * Two items is added to list;
     * Iterating through all items once;
     * Iterating through all items again;
     * Should iterate through both items.
     */
    public function testIteratingMultipleTimes()
    {
        $oList = new BaseList([1, 2]);

        foreach ($oList as $iItem) {
        }

        $aElements = [];
        foreach ($oList as $iItem) {
            $aElements[] = $iItem;
        }

        $this->assertEquals([1, 2], $aElements);
    }

    /**
     * Three items is added to list;
     * Correctly formed array is returned.
     */
    public function testReturningArray()
    {
        $oList = new BaseList([1, 2]);
        $oList->add(3);

        $this->assertEquals([1, 2, 3], $oList->getArray());
    }

    /**
     * Two items is added to empty list;
     * Both items should be correctly added to list;
     */
    public function testAdditionOfItemsToEmptyList()
    {
        $oList = new BaseList();
        $oList->add(1);
        $oList->add(2);

        $this->assertEquals([1, 2], $oList->getArray());
    }

    /**
     * Two items is added to non empty list;
     * Both items should be added to list without deleting existing items;
     */
    public function testAdditionOfItemsToNotEmptyList()
    {
        $oList = new BaseList([1, 2]);
        $oList->add(1);
        $oList->add(2);

        $this->assertEquals([1, 2, 1, 2], $oList->getArray());
    }

    /**
     * Two items is added to list;
     * Counting of items returns correct value.
     */
    public function testCountingOfListItems()
    {
        $oList = new BaseList([1, 2]);

        $this->assertEquals(2, count($oList));
    }
}
