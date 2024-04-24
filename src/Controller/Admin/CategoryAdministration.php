<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EVatModule\Model\CategoryArticlesUpdater;
use OxidEsales\EVatModule\Model\CategoryVATGroupsList;
use OxidEsales\EVatModule\Model\CountryVATGroupsList;

/**
 * Class responsible for TBE services administration using categories.
 */
class CategoryAdministration extends AdminDetailsController
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

        return '@oevattbe/admin/oevattbecategoryadministration';
    }

    /**
     * Updates category information related with TBE.
     */
    public function save()
    {
        parent::save();

        $sCurrentCategoryId = $this->getEditObjectId();
        $request = Registry::getRequest();
        $aParams = $request->getRequestParameter('editval');
        $aVATGroupsParams = $request->getRequestParameter('VATGroupsByCountry');
        $oCategoryVATGroupsList = ContainerFacade::get(CategoryVATGroupsList::class);
        $oCategoryVATGroupsList->setId($sCurrentCategoryId);
        $oCategoryVATGroupsList->setData($aVATGroupsParams);
        $oCategoryVATGroupsList->save();

        /** @var Category $oCategory */
        $oCategory = oxNew(Category::class);
        $oCategory->load($sCurrentCategoryId);
        $oCategory->oxcategories__oevattbe_istbe = new Field($aParams['oevattbe_istbe']);
        $oCategory->save();

        ContainerFacade::get(CategoryArticlesUpdater::class)
            ->addCategoryTBEInformationToArticles($oCategory);
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
        $oCategoryVATGroupsList = ContainerFacade::get(CategoryVATGroupsList::class);
        $oCategoryVATGroupsList->load($this->getEditObjectId());
        if (is_null($this->_aCategoryVATGroupData)) {
            $this->_aCategoryVATGroupData = $oCategoryVATGroupsList->getData();
        }

        if (!isset($this->_aCategoryVATGroupData[$sCountryId])) {
            return false;
        }

        return (int)$this->_aCategoryVATGroupData[$sCountryId] === (int)$sVATGroupId;
    }

    /**
     * Forms view VAT groups data for template.
     *
     * @return array
     */
    public function getCountryAndVATGroupsData()
    {
        /** @var Country $country */
        $country = oxNew(Country::class);
        $aViewData = array();
        $countryVATGroupsList = ContainerFacade::get(CountryVATGroupsList::class);
        $aVATGroupList = $countryVATGroupsList->getList();
        foreach ($aVATGroupList as $sCountryId => $aGroupsList) {
            $country->load($sCountryId);
            $aViewData[$sCountryId] = array(
                'countryTitle' => $country->getFieldData('oxtitle'),
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
        /** @var Category $oCategory */
        $oCategory = oxNew(Category::class);
        $sCurrentCategoryId = $this->getEditObjectId();
        $oCategory->load($sCurrentCategoryId);

        return (int) $oCategory->getFieldData('oevattbe_istbe');
    }
}
