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

/**
 * VAT TBE User class
 */
class oeVATTBETBEUser
{

    /** @var oxUser  */
    private $_oUser = null;

    /** @var oxSession  */
    private $_oSession = null;

    /** @var oxConfig  */
    private $_oConfig = null;

    /**
     * Handles dependencies.
     *
     * @param oxUser    $oUser    User object. Will be used for country calculations.
     * @param oxSession $oSession Communicator with session. Should have setVariable, getVariable methods.
     * @param oxConfig  $oConfig  Communicator with config.
     */
    public function __construct($oUser, $oSession, $oConfig)
    {
        $this->_oUser = $oUser;
        $this->_oSession = $oSession;
        $this->_oConfig = $oConfig;
    }

    /**
     * Returns users TBE country
     *
     * @return array
     */
    public function getOeVATTBEEvidenceList()
    {
        $this->_loadEvidenceDataToSession();
        return $this->_getSession()->getVariable('TBEEvidenceList');
    }

    /**
     * Returns users TBE country
     *
     * @return string
     */
    public function getOeVATTBETbeCountryId()
    {
        $this->_loadEvidenceDataToSession();
        return $this->_getSession()->getVariable('TBECountryId');
    }

    /**
     * Returns users TBE country
     *
     * @return string
     */
    public function getOeVATTBETbeEvidenceUsed()
    {
        $this->_loadEvidenceDataToSession();
        return $this->_getSession()->getVariable('TBEEvidenceUsed');
    }

    /**
     * Unset TBE country from caching to force recalculation on next get.
     */
    public function unsetTbeCountryFromCaching()
    {
        $oSession = $this->_getSession();
        $oSession->deleteVariable('TBEEvidenceList');
        $oSession->deleteVariable('TBECountryId');
        $oSession->deleteVariable('TBEEvidenceUsed');
    }

    /**
     * Returns user object.
     *
     * @return oxUser
     */
    protected function _getUser()
    {
        return $this->_oUser;
    }

    /**
     * Returns session object.
     *
     * @return oxSession
     */
    protected function _getSession()
    {
        return $this->_oSession;
    }

    /**
     * Returns configuration object.
     *
     * @return oxConfig
     */
    protected function _getConfig()
    {
        return $this->_oConfig;
    }

    /**
     * Loads evidence information to session if not already there.
     */
    private function _loadEvidenceDataToSession()
    {
        $oSession = $this->_getSession();
        if (is_null($oSession->getVariable('TBECountryId'))) {
            $oEvidenceSelector = $this->_factoryEvidenceSelector();
            $oSession->setVariable('TBEEvidenceList', $oEvidenceSelector->getEvidenceList()->getArray());

            $oEvidence = $oEvidenceSelector->getEvidence();
            $sTBECountryId = $oEvidence ? $oEvidence->getCountryId() : '';
            $sEvidenceUser = $oEvidence ? $oEvidence->getId() : '';
            $oSession->setVariable('TBECountryId', $sTBECountryId);
            $oSession->setVariable('TBEEvidenceUsed', $sEvidenceUser);
        }
    }

    /**
     * Forms evidence selector object.
     *
     * @return oeVATTBEEvidenceSelector
     */
    private function _factoryEvidenceSelector()
    {
        $oConfig = $this->_getConfig();
        $oUser = $this->_getUser();

        /** @var oeVATTBEEvidenceCollector $oEvidenceCollector */
        $oEvidenceCollector = oxNew('oeVATTBEEvidenceCollector', $oUser, $oConfig);
        $oEvidenceList = $oEvidenceCollector->getEvidenceList();
        $oEvidenceSelector = oxNew('oeVATTBEEvidenceSelector', $oEvidenceList, $oConfig);

        return $oEvidenceSelector;
    }

    /**
     * Returns country
     *
     * @return oxCountry
     */
    public function getCountry()
    {
        $oCountry = null;

        if (!is_null($this->getOeVATTBETbeCountryId())) {
            $oCountry = oxNew('oxCountry');
            $oCountry->load($this->getOeVATTBETbeCountryId());
        }

        return $oCountry;
    }
}
