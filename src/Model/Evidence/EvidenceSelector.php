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

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EVatModule\Model\Evidence\Item\Evidence;

/**
 * Class checks all evidences and provides the one that should be used in VAT calculations.
 */
class EvidenceSelector
{
    /** @var array List of evidences. */
    private $_oEvidenceList = array();

    /** @var Config Configuration object. */
    private $_oConfig = array();

    /**
     * Handles required dependencies.
     *
     * @param EvidenceList $oEvidenceList List of evidences.
     * @param Config             $oConfig       Shop Configuration object.
     */
    public function __construct(EvidenceList $oEvidenceList, Config $oConfig)
    {
        $this->_oEvidenceList = $oEvidenceList;
        $this->_oConfig = $oConfig;
    }

    /**
     * Returns evidence list.
     *
     * @return EvidenceList
     */
    public function getEvidenceList()
    {
        return $this->_oEvidenceList;
    }

    /**
     * Checks all evidences and provides the one that should be used in VAT calculations.
     *
     * @return Evidence|null
     */
    public function getEvidence()
    {
        $oConfig = $this->_getConfig();
        $sDefaultEvidenceName = $oConfig->getConfigParam('sOeVATTBEDefaultEvidence');

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

    /**
     * Returns shop configuration object.
     *
     * @return Config
     */
    protected function _getConfig()
    {
        return $this->_oConfig;
    }
}
