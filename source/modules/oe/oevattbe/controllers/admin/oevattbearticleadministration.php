<?php
/**
 * This file is part of OXID eSales VAT TBE module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales VAT TBE module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014T
 */

/**
 * Class responsible for VAT TBE article administration.
 */
class oeVATTBEArticleAdministration extends oxAdminDetails
{
    /**
     * Renders template for VAT TBE administration in article page.
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = oxNew("oeVATTBEOxArticle");
        $sCurrentArticleId = $this->getEditObjectId();
        $oArticle->load($sCurrentArticleId);
        $this->_aViewData["iIsTbeService"] = $oArticle->isTBEService();
        /** @var oxCountry $oCountry */
        $oCountry = oxNew('oxCountry');
        $this->_aViewData["aTBECountriesAndVATGroups"] = $this->_getCountryAndVATGroupsData($oCountry);
/*echo "<pre/>";
        print_r($this->_aViewData["aTBECountriesAndVATGroups"]);*/
        /** @var oeVATTBEArticleVATGroupsList $oGroupList */
        //$oGroupList = oeVATTBEArticleVATGroupsList::createArticleVATGroupsList();
        //var_dump($oGroupList->load('05848170643ab0deb9914566391c0c63'));
        //var_dump($oGroupList->getData());

        return "oevattbearticleadministration.tpl";
    }

    /**
     * Updates article information related with TBE services.
     */
    public function save()
    {
        parent::save();
        $sCurrentArticleId = $this->getEditObjectId();
        $oConfig = $this->getConfig();
        $aParams = $oConfig->getRequestParameter("editval");
        $aVATGroupsParams = $oConfig->getRequestParameter("VATGroupsByCountry");
        $oArticleVATGroupsList = oeVATTBEArticleVATGroupsList::createArticleVATGroupsList();
        $oArticleVATGroupsList->setId($sCurrentArticleId);
        $oArticleVATGroupsList->setData($aVATGroupsParams);
        $oArticleVATGroupsList->save();

        $iIsTBEService = $aParams['oevattbe_istbeservice'];

        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = oxNew("oeVATTBEOxArticle");
        $oArticle->load($sCurrentArticleId);
        $oArticle->oxarticles__oevattbe_istbeservice = new oxField($iIsTBEService);
        $oArticle->save();
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

    /**
     * Forms country VAT group information for view.
     *
     * @param oeVATTBECountryVATGroup $oCountryVATGroup Object to get information.
     *
     * @return string
     */
    protected function _formGroupInformation($oCountryVATGroup)
    {
        return $oCountryVATGroup->getName() . ' - ' . $oCountryVATGroup->getRate() . '%';
    }
}
