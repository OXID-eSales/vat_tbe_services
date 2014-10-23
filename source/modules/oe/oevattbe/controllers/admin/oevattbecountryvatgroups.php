<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

/**
 * Display VAT groups for particular country.
 */
class oeVATTBECountryVatGroups extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oxOrder object,
     * passes it's data to Smarty engine and returns
     * name of template file "order_paypal.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        return "oevattbecountryvatgroups.tpl";
    }

    /**
     * Return VAT Groups for selected country.
     *
     * @return array
     */
    public function getVatGroups()
    {
        /** @var oeVATTBECountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        /** @var oeVATTBECountryVATGroupsList $oeVATTBECountryVATGroupsList */
        $oVATTBECountryVATGroupsList = oxNew('oeVATTBECountryVATGroupsList', $oGateway);
        $aVATTBECountryVATGroupsList = $oVATTBECountryVATGroupsList->load('a7c40f632e04633c9.47194042');

        return $aVATTBECountryVATGroupsList;
    }

    /**
     * Add country VAT group.
     *
     * @return null
     */
    public function addCountryVATGroup()
    {
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");
        $sCountryId = $aParams['oxcountry__oxid'];
        $sGroupName = $aParams['oevattbe_name'];
        $fVATRate = $aParams['oevattbe_rate'];
        $sGroupDescription = trim($aParams['oevattbe_description']);

        if (!$sCountryId || !$sGroupName || !$fVATRate) {
            $this->_setMissingParameterMessage();
            return null;
        }

        $oGroup = $this->_factoryVATGroup();
        $oGroup->setCountryId($sCountryId);
        $oGroup->setName($sGroupName);
        $oGroup->setRate($fVATRate);
        $oGroup->setDescription($sGroupDescription);
        $oGroup->save();
    }

    /**
     * Method to change Country VAT Groups data.
     */
    public function changeCountryVATGroups()
    {
        $aVatGroups = oxRegistry::getConfig()->getRequestParameter("updateval");

        $oVatGroup = $this->_factoryVATGroup();
        foreach ($aVatGroups as $aVatGroup) {
            $oVatGroup->setId($aVatGroup['oevattbe_id']);
            $oVatGroup->setName($aVatGroup['oevattbe_name']);
            $oVatGroup->setRate($aVatGroup['oevattbe_rate']);
            $oVatGroup->setDescription(trim($aVatGroup['oevattbe_description']));
            $oVatGroup->save();
        }
    }

    /**
     * Create class to deal with VAT Group together with its dependencies.
     *
     * @return oeVATTBECountryVATGroup
     */
    protected function _factoryVATGroup()
    {
        /** @var oeVATTBECountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');

        /** @var oeVATTBECountryVATGroup $oGroup */
        $oGroup = oxNew('oeVATTBECountryVATGroup', $oGateway);
        return $oGroup;
    }

    /**
     * Set error message if some required parameter is missing.
     */
    protected function _setMissingParameterMessage()
    {
        /** @var oxLang $oLang */
        $oLang = oxRegistry::getLang();

        /** @var oxDisplayError $oEx */
        $oEx = oxNew('oxDisplayError');
        $oEx->setMessage($oLang->translateString('OEVATTBE_NEW_COUNTRY_VAT_GROUP_PARAMETER_MISSING', $oLang->getTplLanguage()));

        /** @var oxUtilsView $oView */
        $oView = oxRegistry::get('oxUtilsView');
        $oView->addErrorToDisplay($oEx);
    }
}
