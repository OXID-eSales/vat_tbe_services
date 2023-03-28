<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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

        oeVATTBECategoryArticlesUpdater::createInstance()->addCategoryTBEInformationToArticles($oCategory);
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
