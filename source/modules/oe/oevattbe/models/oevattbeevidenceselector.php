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
 * Class checks all collected evidences and provides user country from them.
 */
class oeVATTBEEvidenceSelector
{
    /** @var array List of evidence collectors */
    private $_oEvidenceList = array();

    /** @var oxConfig Config object */
    private $_oConfig = array();

    /**
     * Class constructor method, handles required dependencies.
     *
     * @param oeVATTBEEvidenceList $oEvidenceList List of evidences.
     * @param oxConfig             $oConfig       Shop Config object.
     */
    public function __construct($oEvidenceList, $oConfig)
    {
        $this->_oEvidenceList = $oEvidenceList;
        $this->_oConfig = $oConfig;
    }

    /**
     * Checks all evidences and returns country from them.
     *
     * @return string
     */
    public function getEvidence()
    {
        $oConfig = $this->_getConfig();
        $sDefaultEvidence = $oConfig->getConfigParam('sDefaultTBEEvidence');

        $oEvidenceList = $this->_getEvidenceList();

        $oFirstNonEmptyEvidence = null;
        $oDefaultEvidence = null;
        foreach ($oEvidenceList as $oEvidence) {
            /** @var oeVATTBEEvidence $oEvidence */
            if ($sDefaultEvidence == $oEvidence->getName()) {
                $oDefaultEvidence = $oEvidence;
            }
            if (!$oFirstNonEmptyEvidence && $oEvidence->getCountryId()) {
                $oFirstNonEmptyEvidence = $oEvidence;
            }
        }

        return $oDefaultEvidence ? $oDefaultEvidence : $oFirstNonEmptyEvidence;
    }

    /**
     * Checks if all countries are unique.
     *
     * @return bool
     */
    public function isEvidencesContradicting()
    {
        $aEvidences = $this->_getEvidenceList();

        $aUniqueCountries = array();
        foreach ($aEvidences as $oEvidence) {
            /** @var oeVATTBEEvidence $oEvidence */
            $aUniqueCountries[$oEvidence->getCountryId()] = $oEvidence->getCountryId();
        }

        return (count($aUniqueCountries) === 1) ? false : true;
    }

    /**
     * Returns evidence list.
     *
     * @return oeVATTBEEvidenceList
     */
    protected function _getEvidenceList()
    {
        return $this->_oEvidenceList;
    }

    /**
     * Returns evidence list.
     *
     * @return oxConfig
     */
    protected function _getConfig()
    {
        return $this->_oConfig;
    }
}
