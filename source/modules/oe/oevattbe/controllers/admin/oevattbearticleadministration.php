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
 * Class responsible for TBE service administration.
 */
class oeVATTBEArticleAdministration extends oxAdminDetails
{
    /** @var array Used to cache VAT Groups data. */
    private $_aArticleVATGroupData;

    /**
     * Renders template for VAT TBE administration in article page.
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = oxNew('oeVATTBEOxArticle');
        $sCurrentArticleId = $this->getEditObjectId();
        $oArticle->load($sCurrentArticleId);
        /** @var oxCountry $oCountry */
        $oCountry = oxNew('oxCountry');

        $this->_aViewData['iIsTbeService'] = $oArticle->isOeVATTBETBEService();
        $this->_aViewData['aCountriesAndVATGroups'] = $this->_getCountryAndVATGroupsData($oCountry);

        return 'oevattbearticleadministration.tpl';
    }

    /**
     * Updates article information related with TBE services.
     */
    public function save()
    {
        parent::save();
        $sCurrentArticleId = $this->getEditObjectId();
        $oConfig = $this->getConfig();
        $aParams = $oConfig->getRequestParameter('editval');
        $aVATGroupsParams = $oConfig->getRequestParameter('VATGroupsByCountry');
        $oArticleVATGroupsList = oeVATTBEArticleVATGroupsList::createArticleVATGroupsList();
        $oArticleVATGroupsList->setId($sCurrentArticleId);
        $oArticleVATGroupsList->setData($aVATGroupsParams);
        $oArticleVATGroupsList->save();

        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = oxNew('oeVATTBEOxArticle');
        $oArticle->load($sCurrentArticleId);
        $oArticle->oxarticles__oevattbe_istbeservice = new oxField($aParams['oevattbe_istbeservice']);
        $oArticle->save();
    }

    /**
     * Used in template to check if select element was selected.
     *
     * @param string $sCountryId  Html select element country.
     * @param string $sVATGroupId Group which is checked.
     *
     * @return bool
     */
    public function isSelected($sCountryId, $sVATGroupId)
    {
        $oArticleVATGroupsList = oeVATTBEArticleVATGroupsList::createArticleVATGroupsList();
        $oArticleVATGroupsList->load($this->getEditObjectId());
        if (is_null($this->_aArticleVATGroupData)) {
            $this->_aArticleVATGroupData = $oArticleVATGroupsList->getData();
        }

        return $this->_aArticleVATGroupData[$sCountryId] === $sVATGroupId;
    }

    /**
     * Forms view VAT groups data for template.
     *
     * @param oxCountry $oCountry Country object used to get country title.
     *
     * @return array
     */
    protected function _getCountryAndVATGroupsData($oCountry)
    {
        $aViewData = array();
        $oCountryVATGroupsList = oeVATTBECountryVATGroupsList::createCountryVATGroupsList();
        $aVATGroupList = $oCountryVATGroupsList->getList();
        foreach ($aVATGroupList as $sCountryId => $aGroupsList) {
            $oCountry->load($sCountryId);
            $aViewData[$sCountryId] = array(
                'countryTitle' => $oCountry->oxcountry__oxtitle->value,
                'countryGroups' => $aGroupsList
            );
        }

        return $aViewData;
    }
}
