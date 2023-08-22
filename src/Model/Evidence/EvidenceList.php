<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
