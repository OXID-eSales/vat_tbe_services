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

namespace OxidEsales\EVatModule\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\oeVATTBECategoryArticlesUpdater;
use OxidEsales\EVatModule\Model\oeVATTBECategoryVATGroupsList;
use OxidEsales\EVatModule\Model\oeVATTBECountryVATGroupsList;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * Class responsible for TBE services administration using categories.
 */
class oeVATTBECategoryAdministration extends AdminDetailsController
{
    use ServiceContainer;

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
        $oCategoryVATGroupsList = $this->getServiceFromContainer(oeVATTBECategoryVATGroupsList::class);
        $oCategoryVATGroupsList->setId($sCurrentCategoryId);
        $oCategoryVATGroupsList->setData($aVATGroupsParams);
        $oCategoryVATGroupsList->save();

        /** @var Category $oCategory */
        $oCategory = oxNew(Category::class);
        $oCategory->load($sCurrentCategoryId);
        $oCategory->oxcategories__oevattbe_istbe = new Field($aParams['oevattbe_istbe']);
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
        $oCategoryVATGroupsList = $this->getServiceFromContainer(oeVATTBECategoryVATGroupsList::class);
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
        /** @var Country $oCountry */
        $oCountry = oxNew(Country::class);
        $aViewData = array();
        $oCountryVATGroupsList = $this->getServiceFromContainer(oeVATTBECountryVATGroupsList::class);
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
        /** @var Category $oCategory */
        $oCategory = oxNew(Category::class);
        $sCurrentCategoryId = $this->getEditObjectId();
        $oCategory->load($sCurrentCategoryId);

        return (int)$oCategory->oxcategories__oevattbe_istbe->value;
    }
}
