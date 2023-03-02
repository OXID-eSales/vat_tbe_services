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

namespace OxidEsales\EVatModule\Model\Evidence;

use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\EVatModule\Model\Evidence\Item\Evidence;
use OxidEsales\EVatModule\Model\BaseList;

/**
 * Class for storing evidences.
 */
class EvidenceList extends BaseList
{
    /**
     * Adds item to list.
     * Checks if this item is instance of Evidence.
     *
     * @param Evidence $oItem Evidence to add to the list.
     *
     * @throws StandardException
     */
    public function add($oItem)
    {
        if (!($oItem instanceof Evidence)) {
            /** @var StandardException $oException */
            $oException = oxNew(StandardException::class, 'Item must be instance or child of Evidence');
            throw $oException;
        }

        parent::add($oItem);
    }

    /**
     * Returns evidences in form of array.
     *
     * @return array
     */
    public function getArray()
    {
        $aItems = parent::getArray();

        $aEvidences = array();
        foreach ($aItems as $oEvidence) {
            /** @var Evidence $oEvidence */
            $aEvidences[$oEvidence->getId()] = array(
                'name' => $oEvidence->getId(),
                'countryId' => $oEvidence->getCountryId()
            );
        }

        return $aEvidences;
    }
}
