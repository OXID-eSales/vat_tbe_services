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
 * Class creates list of evidences.
 */
class oeVATTBEEvidenceCollector
{

    /** @var oxConfig */
    private $_oConfig = null;

    /** @var oxUser User object passed to every evidence. */
    private $_oUser = null;

    /**
     * Handles required dependencies.
     *
     * @param oxUser   $oUser
     * @param oxConfig $oConfig
     */
    public function __construct($oUser, $oConfig)
    {
        $this->_oUser = $oUser;
        $this->_oConfig = $oConfig;
    }

    /**
     * Creates list of evidences and returns it.
     * All evidences must be instance or child of oeVATTBEEvidence.
     *
     * @return oeVATTBEEvidenceList
     */
    public function getEvidences()
    {
        $oConfig = $this->_getConfig();
        $oUser = $this->_getUser();
        $aEvidences = (array) $oConfig->getConfigParam('TBECountryEvidences');

        $oList = new oeVATTBEEvidenceList();

        foreach ($aEvidences as $sEvidenceClass) {
            if (class_exists($sEvidenceClass)) {
                /** @var oeVATTBEEvidence $oEvidence */
                $oEvidence = new $sEvidenceClass($oUser);
                $oList->add($oEvidence);
            }
        }

        return $oList;
    }

    /**
     * Returns config object.
     *
     * @return oxConfig
     */
    protected function _getConfig()
    {
        return $this->_oConfig;
    }

    /**
     * Returns user object.
     * User object is passed to every evidence.
     *
     * @return oxUser
     */
    protected function _getUser()
    {
        return $this->_oUser;
    }
}
