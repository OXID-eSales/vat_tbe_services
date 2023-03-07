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
use OxidEsales\Eshop\Core\DisplayError;
use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\EVatModule\Model\DbGateway\CountryVATGroupsDbGateway;
use OxidEsales\EVatModule\Model\CountryVATGroup;
use OxidEsales\EVatModule\Model\CountryVATGroupsList;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * Display VAT groups for particular country.
 */
class CountryVatGroups extends AdminDetailsController
{
    use ServiceContainer;

    /**
     * To set only one error message in session.
     *
     * @var bool
     */
    private $_blMissingParameterErrorSet = false;

    /**
     * Render returns template.
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        return '@oevattbe/admin/oevattbecountryvatgroups';
    }

    /**
     * Return VAT Groups for selected country.
     *
     * @return array
     */
    public function getVatGroups()
    {
        /** @var CountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew(CountryVATGroupsDbGateway::class);

        /** @var CountryVATGroupsList $oVATTBECountryVATGroupsList */
        $oVATTBECountryVATGroupsList = oxNew(CountryVATGroupsList::class, $oGateway);
        $aVATTBECountryVATGroupsList = $oVATTBECountryVATGroupsList->load($this->getEditObjectId());

        return $aVATTBECountryVATGroupsList;
    }

    /**
     * Add country VAT group.
     *
     * @return null
     */
    public function addCountryVATGroup()
    {
        $aParams = Registry::getRequest()->getRequestParameter('editval');
        $sCountryId = $aParams['oxcountry__oxid'];
        $sGroupName = $aParams['oevattbe_name'];
        $fVATRate = $aParams['oevattbe_rate'];
        $sGroupDescription = trim($aParams['oevattbe_description']);

        if (!$sCountryId || !$sGroupName) {
            $this->setMissingParameterMessage();
            return null;
        }

        $oGroup = $this->getServiceFromContainer(CountryVATGroup::class);
        $oGroup->setCountryId($sCountryId);
        $oGroup->setName($sGroupName);
        $oGroup->setRate($fVATRate);
        $oGroup->setDescription($sGroupDescription);
        $oGroup->save();

        $this->_aViewData['updatelist'] = '1';
    }

    /**
     * Method to change Country VAT Groups data.
     */
    public function changeCountryVATGroups()
    {
        $aVatGroups = Registry::getRequest()->getRequestParameter('updateval');

        $oVatGroup = $this->getServiceFromContainer(CountryVATGroup::class);
        foreach ($aVatGroups as $aVatGroup) {
            if (!$aVatGroup['oevattbe_id'] || !$aVatGroup['oevattbe_name']) {
                if (!$this->_blMissingParameterErrorSet) {
                    $this->_blMissingParameterErrorSet = true;
                    $this->setMissingParameterMessage();
                }
                continue;
            }
            $oVatGroup->setId($aVatGroup['oevattbe_id']);
            $oVatGroup->setName($aVatGroup['oevattbe_name']);
            $oVatGroup->setRate($aVatGroup['oevattbe_rate']);
            $oVatGroup->setDescription(trim($aVatGroup['oevattbe_description']));
            $oVatGroup->save();
        }
    }

    /**
     * Delete selected Country VAT Group.
     */
    public function deleteCountryVatGroup()
    {
        $iVATGroupId = Registry::getRequest()->getRequestParameter('countryVATGroupId');

        $oVATGroup = $this->getServiceFromContainer(CountryVATGroup::class);
        $oVATGroup->setId($iVATGroupId);
        $oVATGroup->delete();

        $this->_aViewData['updatelist'] = '1';
    }

    /**
     * Set error message if some required parameter is missing.
     */
    protected function setMissingParameterMessage()
    {
        /** @var Language $oLang */
        $oLang = Registry::getLang();

        /** @var DisplayError $oEx */
        $oEx = oxNew(DisplayError::class);
        $oEx->setMessage($oLang->translateString('OEVATTBE_NEW_COUNTRY_VAT_GROUP_PARAMETER_MISSING', $oLang->getTplLanguage()));

        /** @var UtilsView $oView */
        $oView = Registry::getUtilsView();
        $oView->addErrorToDisplay($oEx);
    }
}
