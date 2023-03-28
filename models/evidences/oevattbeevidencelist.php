<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

require_once  __DIR__ . '/../oevattbelist.php';

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
            $aEvidences[$oEvidence->getId()] = array(
                'name' => $oEvidence->getId(),
                'countryId' => $oEvidence->getCountryId()
            );
        }

        return $aEvidences;
    }
}
