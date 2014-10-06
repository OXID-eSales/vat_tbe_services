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
 * @copyright (C) OXID eSales AG 2003-2014T
 */


/**
 * Base class for lists. Implements Iterator and Countable interfaces.
 */
class oeVATTBEList implements Iterator, Countable
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
    public function getArray()
    {
        return $this->_aItems;
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current($this->_aItems);
    }

    /**
     * Move forward to next element
     */
    public function next()
    {
        next($this->_aItems);
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->_aItems);
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return key($this->_aItems) !== null;
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind()
    {
        reset($this->_aItems);
    }

    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->_aItems);
    }
}
