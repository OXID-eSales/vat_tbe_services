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
     * Add country VAT group.
     */
    public function addCountryVATGroup()
    {
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");

        $oGroup = $this->_factoryVATGroup();
        $oGroup->setCountryId($aParams['oxcountry__oxid']);
        $oGroup->setName($aParams['oevattbe_name']);
        $oGroup->setRate($aParams['oevattbe_rate']);
        $oGroup->setDescription($aParams['oevattbe_description']);
        $oGroup->save();
    }

    /**
     * Create class to deal with VAT Group together with its dependencies.
     *
     * @return oeVATTBEVATGroup
     */
    protected function _factoryVATGroup()
    {
        /** @var oeVATTBEOrderEvidenceListDbGateway $oGateway */
        $oGateway = oxNew('oeVATTBEVATGroupsDbGateway');

        /** @var oeVATTBEVATGroup $oGroup */
        $oGroup = oxNew('oeVATTBEVATGroup', $oGateway);
        return $oGroup;
    }
}
