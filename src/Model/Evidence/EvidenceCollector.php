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
        $aEvidenceClasses = (array) $this->config->getConfigParam('aOeVATTBECountryEvidenceClasses');
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
                $oEvidence = $this->getServiceFromContainer($sEvidenceClass);
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
