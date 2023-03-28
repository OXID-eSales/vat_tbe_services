<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */


/**
 * Class creates list of evidences.
 */
class oeVATTBEEvidenceCollector
{

    /** @var oxConfig Config object to find out about existing evidences */
    private $_oConfig = null;

    /** @var oxUser User object passed to every evidence. */
    private $_oUser = null;

    /**
     * Handles required dependencies.
     *
     * @param oxUser   $oUser   User object passed to every evidence.
     * @param oxConfig $oConfig Config object to find out about existing evidences.
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
    public function getEvidenceList()
    {
        $oConfig = $this->_getConfig();
        $aEvidenceClasses = (array) $oConfig->getConfigParam('aOeVATTBECountryEvidenceClasses');
        $aEvidences = (array) $oConfig->getConfigParam('aOeVATTBECountryEvidences');

        /** @var oeVATTBEEvidenceList $oList */
        $oList = oxNew('oeVATTBEEvidenceList');
        $aUpdatedEvidences = $this->_fillEvidenceList($oList, $aEvidenceClasses, $aEvidences);

        if ($aEvidences !== $aUpdatedEvidences) {
            oxRegistry::getConfig()->saveShopConfVar('aarr', 'aOeVATTBECountryEvidences', $aUpdatedEvidences, null, 'module:oevattbe');
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

    /**
     * Fills provided evidence list with available active evidences and returns updated active evidences array.
     *
     * @param oeVATTBEEvidenceList $oList
     * @param array                $aEvidenceClasses
     * @param array                $aEvidences
     *
     * @return array
     */
    private function _fillEvidenceList($oList, $aEvidenceClasses, $aEvidences)
    {
        $oUser = $this->_getUser();
        $aUpdatedEvidences = array();

        foreach ($aEvidenceClasses as $sEvidenceClass) {
            if (class_exists($sEvidenceClass)) {
                /** @var oeVATTBEEvidence $oEvidence */
                $oEvidence = oxNew($sEvidenceClass, $oUser);
                $sName = $oEvidence->getId();
                if (isset($aEvidences[$sName]) && $aEvidences[$sName] == 1) {
                    $oList->add($oEvidence);
                }
                $aUpdatedEvidences[$sName] = isset($aEvidences[$sName]) ? $aEvidences[$sName] : 0;
            }
        }

        return $aUpdatedEvidences;
    }
}
