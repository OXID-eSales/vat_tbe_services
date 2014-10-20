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
 * Class for storing evidences.
 */
class oeVATTBEEvidenceList extends oeVATTBEList
{

    /**
     * Adds item to list.
     * Checks if this item is instance of oeVATTBEEvidence.
     *
     * @param oeVATTBEEvidence $oItem Evidence to add to the list.
     *
     * @throws oxException
     */
    public function add($oItem)
    {
        if (!($oItem instanceof oeVATTBEEvidence)) {
            /** @var oxException $oException */
            $oException = oxNew('oxException', 'Item must be instance or child of oeVATTBEEvidence');
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
            /** @var oeVATTBEEvidence $oEvidence */
            $aEvidences[$oEvidence->getName()] = array(
                'name' => $oEvidence->getName(),
                'countryId' => $oEvidence->getCountryId()
            );
        }

        return $aEvidences;
    }
}
