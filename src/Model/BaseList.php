<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model;

/**
 * Base class for lists. Implements Iterator and Countable interfaces.
 */
class BaseList implements \Iterator, \Countable
{
    /** @var array Array of items to work with. */
    private $_aItems = array();

    /**
     * Sets items to work with.
     *
     * @param array $aItems Array of items.
     */
    public function __construct($aItems = array())
    {
        $this->_aItems = $aItems;
    }

    /**
     * Adds item to list.
     *
     * @param mixed $mItem Any item to add to the list.
     */
    public function add($mItem)
    {
        $this->_aItems[] = $mItem;
    }

    /**
     * Returns all items in list in form of array.
     *
     * @return array
     */
    public function getArray(): array
    {
        return $this->_aItems;
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current(): mixed
    {
        return current($this->_aItems);
    }

    /**
     * Move forward to next element
     */
    public function next(): void
    {
        next($this->_aItems);
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key(): mixed
    {
        return key($this->_aItems);
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid(): bool
    {
        return key($this->_aItems) !== null;
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind(): void
    {
        reset($this->_aItems);
    }

    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     * The return value is cast to an integer.
     */
    public function count(): int
    {
        return count($this->_aItems);
    }
}
