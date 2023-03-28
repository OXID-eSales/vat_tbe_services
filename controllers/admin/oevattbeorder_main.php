<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Adds additional functionality needed for oeVATTBE module when managing orders.
 */
class oeVATTBEOrder_Main extends oeVATTBEOrder_Main_parent
{
    /**
     * Returns template name from parent and sets values for template.
     *
     * @return string
     */
    public function render()
    {
        $sOrderId = $this->getEditObjectId();

        /** @var oxOrder $oOrder */
        $oOrder = oxNew('oxOrder');
        $sOrderEvidenceId = $this->_getOeVATTBECurrentOrderEvidenceId($oOrder, $sOrderId);

        $oEvidenceList = oeVATTBEOrderEvidenceList::createInstance();
        $oEvidenceList->loadWithCountryNames($sOrderId);
        $aEvidencesData = $oEvidenceList->getData();

        $this->_aViewData['sTBECountry'] = $aEvidencesData[$sOrderEvidenceId]['countryTitle'];
        $this->_aViewData['aEvidenceUsed'] = $sOrderEvidenceId;
        $this->_aViewData['aEvidencesData'] = $aEvidencesData;

        return parent::render();
    }

    /**
     * Returns currently selected order evidence id.
     *
     * @param oxOrder $oOrder   Used to get evidence id.
     * @param string  $sOrderId Order id.
     *
     * @return string
     */
    protected function _getOeVATTBECurrentOrderEvidenceId($oOrder, $sOrderId)
    {
        $oOrder->load($sOrderId);
        $sEvidenceId = $oOrder->oxorder__oevattbe_evidenceused->value;

        return $sEvidenceId;
    }
}
