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
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\EVatModule\Model\Evidence\EvidenceSelector;
use OxidEsales\EVatModule\Service\ModuleSettings;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * VAT TBE User class
 */
class User
{
    use ServiceContainer;

    public function __construct(
        private Session $session,
        private Config $config,
    ) {
    }

    /**
     * Returns users TBE country
     *
     * @return array
     */
    public function getOeVATTBEEvidenceList()
    {
        $this->loadEvidenceDataToSession();
        return $this->session->getVariable('TBEEvidenceList');
    }

    /**
     * Returns users TBE country
     *
     * @return string
     */
    public function getOeVATTBETbeCountryId()
    {
        $this->loadEvidenceDataToSession();
        return $this->session->getVariable('TBECountryId');
    }

    /**
     * Returns users TBE country
     *
     * @return string
     */
    public function getOeVATTBETbeEvidenceUsed()
    {
        $this->loadEvidenceDataToSession();
        return $this->session->getVariable('TBEEvidenceUsed');
    }

    /**
     * Unset TBE country from caching to force recalculation on next get.
     */
    public function unsetOeVATTBETbeCountryFromCaching()
    {
        $this->session->deleteVariable('TBEEvidenceList');
        $this->session->deleteVariable('TBECountryId');
        $this->session->deleteVariable('TBEEvidenceUsed');
    }

    public function getCountry(): ?EshopCountry
    {
        $countryId = $this->getOeVATTBETbeCountryId();
        $country = oxNew(EShopCountry::class);

        return $country->load($countryId) ? $country : null;
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
     * Loads evidence information to session if not already there.
     */
    private function loadEvidenceDataToSession()
    {
        if (is_null($this->session->getVariable('TBECountryId'))) {
            /** @var EvidenceSelector $evidenceSelector */
            $evidenceSelector = $this->getServiceFromContainer(EvidenceSelector::class);
            $this->session->setVariable('TBEEvidenceList', $evidenceSelector->getEvidenceList()->getArray());

            $oEvidence = $evidenceSelector->getEvidence();
            $sTBECountryId = $oEvidence ? $oEvidence->getCountryId() : '';
            $sEvidenceUser = $oEvidence ? $oEvidence->getId() : '';
            $this->session->setVariable('TBECountryId', $sTBECountryId);
            $this->session->setVariable('TBEEvidenceUsed', $sEvidenceUser);
        }
    }
}
