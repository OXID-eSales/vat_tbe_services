<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model;

use OxidEsales\Eshop\Application\Model\Country as EShopCountry;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EVatModule\Model\Evidence\EvidenceSelector;
use OxidEsales\EVatModule\Service\ModuleSettings;

/**
 * VAT TBE User class
 */
class User
{
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
        $sDomesticCountryAbbr = ContainerFacade::get(ModuleSettings::class)->getDomesticCountry();

        $userCountry = $this->getCountry();

        if ($userCountry) {
            $blResult = $userCountry->getFieldData('oxisoalpha2') == $sDomesticCountryAbbr;
            return $sDomesticCountryAbbr ? $blResult : false;
        }

        return false;
    }

    /**
     * Loads evidence information to session if not already there.
     */
    private function loadEvidenceDataToSession()
    {
        if (is_null($this->session->getVariable('TBECountryId'))) {
            /** @var EvidenceSelector $evidenceSelector */
            $evidenceSelector = ContainerFacade::get(EvidenceSelector::class);
            $this->session->setVariable('TBEEvidenceList', $evidenceSelector->getEvidenceList()->getArray());

            $oEvidence = $evidenceSelector->getEvidence();
            $sTBECountryId = $oEvidence ? $oEvidence->getCountryId() : '';
            $sEvidenceUser = $oEvidence ? $oEvidence->getId() : '';
            $this->session->setVariable('TBECountryId', $sTBECountryId);
            $this->session->setVariable('TBEEvidenceUsed', $sEvidenceUser);
        }
    }
}
