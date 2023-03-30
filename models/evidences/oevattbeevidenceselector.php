<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Class checks all evidences and provides the one that should be used in VAT calculations.
 */
class oeVATTBEEvidenceSelector
{
    /** @var array List of evidences. */
    private $_oEvidenceList = array();

    /** @var oxConfig Configuration object. */
    private $_oConfig = array();

    /**
     * Handles required dependencies.
     *
     * @param oeVATTBEEvidenceList $oEvidenceList List of evidences.
     * @param oxConfig             $oConfig       Shop Configuration object.
     */
    public function __construct($oEvidenceList, $oConfig)
    {
        $this->_oEvidenceList = $oEvidenceList;
        $this->_oConfig = $oConfig;
    }

    /**
     * Returns evidence list.
     *
     * @return oeVATTBEEvidenceList
     */
    public function getEvidenceList()
    {
        return $this->_oEvidenceList;
    }

    /**
     * Checks all evidences and provides the one that should be used in VAT calculations.
     *
     * @return oeVATTBEEvidence|null
     */
    public function getEvidence()
    {
        $oConfig = $this->_getConfig();
        $sDefaultEvidenceName = $oConfig->getConfigParam('sOeVATTBEDefaultEvidence');

        $oEvidenceList = $this->getEvidenceList();

        $oFirstNonEmptyEvidence = null;
        $oDefaultEvidence = null;
        foreach ($oEvidenceList as $oEvidence) {
            /** @var oeVATTBEEvidence $oEvidence */
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
            /** @var oeVATTBEEvidence $oEvidence */
            $aUniqueCountries[$oEvidence->getCountryId()] = $oEvidence->getCountryId();
        }

        return (count($aUniqueCountries) === 1) ? false : true;
    }

    /**
     * Returns shop configuration object.
     *
     * @return oxConfig
     */
    protected function _getConfig()
    {
        return $this->_oConfig;
    }
}
