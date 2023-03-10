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
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\Evidence\Item\BillingCountryEvidence;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Shop\User as EShopUser;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * Class for registering evidences.
 * After registration these evidences will be available
 */
class EvidenceRegister
{
    use ServiceContainer;

    /** @var ModuleSettings */
    private $moduleSettings;

    /**
     * Handles class dependencies.
     *
     * @param Config $config Shop configuration object.
     */
    public function __construct(
        private Config $config
    ) {
        $this->moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
    }

    /**
     * Returns registered evidences.
     *
     * @return array
     */
    public function getRegisteredEvidences()
    {
        return (array) $this->config->getConfigParam('aOeVATTBECountryEvidenceClasses');
    }

    /**
     * Returns active evidences.
     *
     * @return array
     */
    public function getActiveEvidences()
    {
        return $this->moduleSettings->getCountryEvidences();
    }

    /**
     * Registers evidence to config.
     * This evidence will be used when calculating user country.
     * By default registered evidence is made inactive,
     * but shop administrator can go to module configuration and activate it.
     *
     * @param string $sEvidenceClass Evidence class name. It should be reachable by shop (e.g. in activated module).
     * @param bool   $blActive       Whether to make this evidence active after registration. Default is false.
     */
    public function registerEvidence($sEvidenceClass, $blActive = false)
    {
        $aEvidences = $this->getRegisteredEvidences();
        if (class_exists($sEvidenceClass)) {
            if (!in_array($sEvidenceClass, $aEvidences)) {
                $aEvidences[] = $sEvidenceClass;
                Registry::getConfig()->saveShopConfVar('arr', 'aOeVATTBECountryEvidenceClasses', $aEvidences);
            }

            $this->addEvidenceToEvidenceList($sEvidenceClass, $blActive);
        }
    }

    /**
     * Registers evidence to config.
     * This evidence will be used when calculating user country.
     * By default registered evidence is made inactive,
     * but shop administrator can go to module configuration and activate it.
     *
     * @param string $sEvidenceClass Evidence class name. It should be reachable by shop (e.g. in activated module).
     * @param string $sEvidenceId    Evidence id. In case no evidence id is given, evidence class must be still reachable.
     */
    public function unregisterEvidence($sEvidenceClass, $sEvidenceId = null)
    {
        $aEvidenceClasses = $this->getRegisteredEvidences();
        if (($key = array_search($sEvidenceClass, $aEvidenceClasses)) !== false) {
            unset($aEvidenceClasses[$key]);
            Registry::getConfig()->saveShopConfVar('arr', 'aOeVATTBECountryEvidenceClasses', $aEvidenceClasses);
        }
        $this->removeEvidenceToEvidenceList($sEvidenceClass, $sEvidenceId);
    }

    /**
     * Activates evidence by id.
     *
     * @param string $sEvidenceId Evidence Id. This is should be defined in evidence class.
     */
    public function activateEvidence($sEvidenceId)
    {
        $aEvidences = $this->getActiveEvidences();
        if (isset($aEvidences[$sEvidenceId]) && $aEvidences[$sEvidenceId] !== 1) {
            $aEvidences[$sEvidenceId] = 1;
            $this->moduleSettings->saveCountryEvidences($aEvidences);
        }
    }

    /**
     * Deactivates evidence by id.
     *
     * @param string $sEvidenceId Evidence Id. This is should be defined in evidence class.
     */
    public function deactivateEvidence($sEvidenceId)
    {
        $aEvidences = $this->getActiveEvidences();
        if (isset($aEvidences[$sEvidenceId]) && $aEvidences[$sEvidenceId] !== 0) {
            $aEvidences[$sEvidenceId] = 0;
            $this->moduleSettings->saveCountryEvidences($aEvidences);
        }
    }

    /**
     * Adds evidence id to evidence list. If $blActive is set to true, also activates it.
     * If evidence is already in the list, its activation state will not be changed.
     *
     * @param string $sEvidenceClass Evidence class name.
     * @param bool   $blActive       Whether to activate evidence.
     */
    private function addEvidenceToEvidenceList($sEvidenceClass, $blActive = false)
    {
        $aEvidences = $this->getActiveEvidences();
        $sEvidenceId = $this->getEvidenceId($sEvidenceClass);
        if (!isset($aEvidences[$sEvidenceId])) {
            $aEvidences[$sEvidenceId] = $blActive ? 1 : 0;
            $this->moduleSettings->saveCountryEvidences($aEvidences);
        }
    }

    /**
     * Adds evidence id to evidence list. If $blActive is set to true, also activates it.
     * If evidence is already in the list, its activation state will not be changed.
     *
     * @param string $sEvidenceClass Evidence class name.
     * @param bool   $sEvidenceId    Whether to activate evidence.
     */
    private function removeEvidenceToEvidenceList($sEvidenceClass, $sEvidenceId = false)
    {
        if (!$sEvidenceId && class_exists($sEvidenceClass)) {
            $sEvidenceId = $this->getEvidenceId($sEvidenceClass);
        }

        $aEvidences = $this->getActiveEvidences();
        if (isset($aEvidences[$sEvidenceId])) {
            unset($aEvidences[$sEvidenceId]);
            $this->moduleSettings->saveCountryEvidences($aEvidences);
        }
    }

    /**
     * Returns evidence id by its class name. Does not check if evidence class exist,
     * so validation of the must be done before.
     *
     * @param string $sEvidenceClass Evidence class name.
     *
     * @return string
     */
    private function getEvidenceId($sEvidenceClass)
    {
        $user = oxNew(EShopUser::class);

        /** @var BillingCountryEvidence $oEvidence */
        $oEvidence = oxNew($sEvidenceClass, $user);
        return $oEvidence->getId();
    }
}
