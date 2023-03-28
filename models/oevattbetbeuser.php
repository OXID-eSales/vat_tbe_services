<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
     * Creates self instance.
     *
     * @return oeVATTBETBEUser
     */
    public static function createInstance()
    {
        $oSession = oxRegistry::getSession();
        $oConfig = oxRegistry::getConfig();
        $oUser = $oSession->getUser();

        /** @var oeVATTBETBEUser $oInstance */
        $oInstance = oxNew('oeVATTBETBEUser', $oUser, $oSession, $oConfig);
        return $oInstance;
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
    public function unsetOeVATTBETbeCountryFromCaching()
    {
        $oSession = $this->_getSession();
        $oSession->deleteVariable('TBEEvidenceList');
        $oSession->deleteVariable('TBECountryId');
        $oSession->deleteVariable('TBEEvidenceUsed');
    }

    /**
     * Returns country object. If country was not found, returns null.
     *
     * @return oxCountry|oeVATTBEOxCountry|null
     */
    public function getCountry()
    {
        $oCountry = null;

        $sCountryId = $this->getOeVATTBETbeCountryId();
        $oCountry = oxNew('oxCountry');
        if (!$oCountry->load($sCountryId)) {
            $oCountry = null;
        }

        return $oCountry;
    }

    /**
     * Returns whether user is from domestic country.
     *
     * @return bool
     */
    public function isUserFromDomesticCountry()
    {
        $sDomesticCountryAbbr = $this->_getConfig()->getConfigParam('sOeVATTBEDomesticCountry');
        $oUserCountry = $this->getCountry();

        $blResult = $oUserCountry->oxcountry__oxisoalpha2->value == $sDomesticCountryAbbr;

        return $sDomesticCountryAbbr ? $blResult : false;
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
}
