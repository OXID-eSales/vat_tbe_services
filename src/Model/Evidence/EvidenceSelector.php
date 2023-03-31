<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model\Evidence;

use OxidEsales\EVatModule\Model\Evidence\Item\Evidence;
use OxidEsales\EVatModule\Service\ModuleSettings;

/**
 * Class checks all evidences and provides the one that should be used in VAT calculations.
 */
class EvidenceSelector
{
    public function __construct(
        private ModuleSettings $moduleSettings,
        private EvidenceCollector $evidenceCollector
    ) {
    }

    /**
     * Returns evidence list.
     *
     * @return EvidenceList
     */
    public function getEvidenceList()
    {
        return $this->evidenceCollector->getEvidenceList();
    }

    /**
     * Checks all evidences and provides the one that should be used in VAT calculations.
     *
     * @return Evidence|null
     */
    public function getEvidence()
    {
        $sDefaultEvidenceName = $this->moduleSettings->getDefaultEvidence();

        $oEvidenceList = $this->getEvidenceList();

        $oFirstNonEmptyEvidence = null;
        $oDefaultEvidence = null;
        foreach ($oEvidenceList as $oEvidence) {
            /** @var Evidence $oEvidence */
            if ($oEvidence->getCountryId()) {
                if ($sDefaultEvidenceName == $oEvidence->getId()) {
                    $oDefaultEvidence = $oEvidence;
                }
                if (!$oFirstNonEmptyEvidence) {
                    $oFirstNonEmptyEvidence = $oEvidence;
                }
            }
        }

        return $oDefaultEvidence ? $oDefaultEvidence : $oFirstNonEmptyEvidence;
    }

    /**
     * Checks if there are any contradicting evidences.
     *
     * @return bool
     */
    public function isEvidencesContradicting()
    {
        $aEvidences = $this->getEvidenceList();

        $aUniqueCountries = array();
        foreach ($aEvidences as $oEvidence) {
            /** @var Evidence $oEvidence */
            $aUniqueCountries[$oEvidence->getCountryId()] = $oEvidence->getCountryId();
        }

        return (count($aUniqueCountries) === 1) ? false : true;
    }
}
