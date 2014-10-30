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
 * Class responsible for TBE services administration using categories.
 */
class oeVATTBECategoryAdministration extends oxAdminDetails
{
    /** @var array Used to cache VAT Groups data. */
    private $_aCategoryVATGroupData = null;

    /**
     * Renders template for VAT TBE administration in category page.
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        return 'oevattbecategoryadministration.tpl';
    }

    /**
     * Updates category information related with TBE.
     */
    public function save()
    {
        parent::save();
        $sCurrentCategoryId = $this->getEditObjectId();
        $oConfig = $this->getConfig();
        $aParams = $oConfig->getRequestParameter('editval');
        $aVATGroupsParams = $oConfig->getRequestParameter('VATGroupsByCountry');
        $oCategoryVATGroupsList = oeVATTBECategoryVATGroupsList::createInstance();
        $oCategoryVATGroupsList->setId($sCurrentCategoryId);
        $oCategoryVATGroupsList->setData($aVATGroupsParams);
        $oCategoryVATGroupsList->save();

        /** @var oxCategory $oCategory */
        $oCategory = oxNew('oxCategory');
        $oCategory->load($sCurrentCategoryId);
        $oCategory->oxcategories__oevattbe_istbe = new oxField($aParams['oevattbe_istbe']);
        $oCategory->save();
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
        $oCategoryVATGroupsList = oeVATTBECategoryVATGroupsList::createInstance();
        $oCategoryVATGroupsList->load($this->getEditObjectId());
        if (is_null($this->_aCategoryVATGroupData)) {
            $this->_aCategoryVATGroupData = $oCategoryVATGroupsList->getData();
        }

        return $this->_aCategoryVATGroupData[$sCountryId] === $sVATGroupId;
    }

    /**
     * Forms view VAT groups data for template.
     *
     * @return array
     */
    public function getCountryAndVATGroupsData()
    {
        /** @var oxCountry $oCountry */
        $oCountry = oxNew('oxCountry');
        $aViewData = array();
        $oCountryVATGroupsList = oeVATTBECountryVATGroupsList::createInstance();
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
     * Returns if selected category is TBE.
     *
     * @return int
     */
    public function isCategoryTBE()
    {
        /** @var oxCategory $oCategory */
        $oCategory = oxNew('oxCategory');
        $sCurrentCategoryId = $this->getEditObjectId();
        $oCategory->load($sCurrentCategoryId);

        return (int)$oCategory->oxcategories__oevattbe_istbe->value;
    }
}
