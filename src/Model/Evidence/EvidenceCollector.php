<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model\Evidence;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EVatModule\Model\Evidence\Item\Evidence;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * Class creates list of evidences.
 */
class EvidenceCollector
{
    use ServiceContainer;

    /**
     * Handles required dependencies.
     *
     * @param Config $config Config object to find out about existing evidences.
     * @param ModuleSettings $moduleSettings Config object to find out about existing evidences.
     */
    public function __construct(
        private Config $config,
        private ModuleSettings $moduleSettings
    ) {
    }

    /**
     * Creates list of evidences and returns it.
     * All evidences must be instance or child of Evidence.
     *
     * @return EvidenceList
     */
    public function getEvidenceList()
    {
        $aEvidenceClasses = $this->moduleSettings->getEvidenceClasses();
        $aEvidences = $this->moduleSettings->getCountryEvidences();

        /** @var EvidenceList $oList */
        $oList = oxNew(EvidenceList::class);
        $aUpdatedEvidences = $this->fillEvidenceList($oList, $aEvidenceClasses, $aEvidences);

        if ($aEvidences !== $aUpdatedEvidences) {
            $this->moduleSettings->saveCountryEvidences($aUpdatedEvidences);
        }

        return $oList;
    }

    /**
     * Fills provided evidence list with available active evidences and returns updated active evidences array.
     *
     * @param EvidenceList $oList
     * @param array        $aEvidenceClasses
     * @param array        $aEvidences
     *
     * @return array
     */
    private function fillEvidenceList($oList, $aEvidenceClasses, $aEvidences)
    {
        $aUpdatedEvidences = array();

        foreach ($aEvidenceClasses as $sEvidenceClass) {
            if (class_exists($sEvidenceClass)) {
                /** @var Evidence $oEvidence */
                $oEvidence = oxNew($sEvidenceClass, Registry::getSession());
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
