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

namespace OxidEsales\EVatModule\Model;

use OxidEsales\Eshop\Application\Model\Country as EShopCountry;
use OxidEsales\Eshop\Application\Model\User as EShopUser;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\EVatModule\Model\Evidence\EvidenceCollector;
use OxidEsales\EVatModule\Model\Evidence\EvidenceSelector;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Shop\Country;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * VAT TBE User class
 */
class User
{
    use ServiceContainer;

    /** @var EShopUser  */
    private $_oUser = null;

    /** @var Session  */
    private $_oSession = null;

    /** @var Config  */
    private $_oConfig = null;

    /**
     * Handles dependencies.
     *
     * @param EShopUser    $oUser    User object. Will be used for country calculations.
     * @param Session $oSession Communicator with session. Should have setVariable, getVariable methods.
     * @param Config  $oConfig  Communicator with config.
     */
    public function __construct(EShopUser $oUser, Session $oSession, Config $oConfig)
    {
        $this->_oUser = $oUser;
        $this->_oSession = $oSession;
        $this->_oConfig = $oConfig;
    }

    /**
     * Creates self instance.
     *
     * @return User
     */
    public static function createInstance()
    {
        $oSession = Registry::getSession();
        $oConfig = Registry::getConfig();
        $oUser = $oSession->getUser();
        //todo: do proper implement
        if($oUser === false) {
            $oUser = oxNew(EShopUser::class);
        }

        /** @var User $oInstance */
        $oInstance = oxNew(User::class, $oUser, $oSession, $oConfig);
        return $oInstance;
    }

    /**
     * Returns users TBE country
     *
     * @return array
     */
    public function getOeVATTBEEvidenceList()
    {
        $this->loadEvidenceDataToSession();
        return $this->getSession()->getVariable('TBEEvidenceList');
    }

    /**
     * Returns users TBE country
     *
     * @return string
     */
    public function getOeVATTBETbeCountryId()
    {
        $this->loadEvidenceDataToSession();
        return $this->getSession()->getVariable('TBECountryId');
    }

    /**
     * Returns users TBE country
     *
     * @return string
     */
    public function getOeVATTBETbeEvidenceUsed()
    {
        $this->loadEvidenceDataToSession();
        return $this->getSession()->getVariable('TBEEvidenceUsed');
    }

    /**
     * Unset TBE country from caching to force recalculation on next get.
     */
    public function unsetOeVATTBETbeCountryFromCaching()
    {
        $oSession = $this->getSession();
        $oSession->deleteVariable('TBEEvidenceList');
        $oSession->deleteVariable('TBECountryId');
        $oSession->deleteVariable('TBEEvidenceUsed');
    }

    /**
     * Returns country object. If country was not found, returns null.
     *
     * @return EShopCountry|Country|null
     */
    public function getCountry()
    {
        $oCountry = null;

        $sCountryId = $this->getOeVATTBETbeCountryId();
        $oCountry = oxNew(EShopCountry::class);
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
        $sDomesticCountryAbbr = $this->getServiceFromContainer(ModuleSettings::class)->getDomesticCountry();

        $oUserCountry = $this->getCountry();

        $blResult = $oUserCountry->oxcountry__oxisoalpha2->value == $sDomesticCountryAbbr;

        return $sDomesticCountryAbbr ? $blResult : false;
    }

    /**
     * Returns user object.
     *
     * @return EShopUser
     */
    protected function getUser()
    {
        return $this->_oUser;
    }

    /**
     * Returns session object.
     *
     * @return Session
     */
    protected function getSession()
    {
        return $this->_oSession;
    }

    /**
     * Returns configuration object.
     *
     * @return Config
     */
    protected function getConfig()
    {
        return $this->_oConfig;
    }

    /**
     * Loads evidence information to session if not already there.
     */
    private function loadEvidenceDataToSession()
    {
        $oSession = $this->getSession();
        if (is_null($oSession->getVariable('TBECountryId'))) {
            $oEvidenceSelector = $this->factoryEvidenceSelector();
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
     * @return EvidenceSelector
     */
    private function factoryEvidenceSelector()
    {
        $oConfig = $this->getConfig();
        $oUser = $this->getUser();

        /** @var EvidenceCollector $oEvidenceCollector */
        $oEvidenceCollector = oxNew(EvidenceCollector::class, $oUser, $oConfig);
        $oEvidenceList = $oEvidenceCollector->getEvidenceList();
        $oEvidenceSelector = oxNew(EvidenceSelector::class, $oEvidenceList, $oConfig);

        return $oEvidenceSelector;
    }
}
