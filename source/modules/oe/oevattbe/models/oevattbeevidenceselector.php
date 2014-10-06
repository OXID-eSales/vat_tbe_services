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
        $sDefaultEvidenceName = $oConfig->getConfigParam('sDefaultTBEEvidence');

        $oEvidenceList = $this->getEvidenceList();

        $oFirstNonEmptyEvidence = null;
        $oDefaultEvidence = null;
        foreach ($oEvidenceList as $oEvidence) {
            /** @var oeVATTBEEvidence $oEvidence */
            if ($oEvidence->getCountryId()) {
                if ($sDefaultEvidenceName == $oEvidence->getName()) {
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
