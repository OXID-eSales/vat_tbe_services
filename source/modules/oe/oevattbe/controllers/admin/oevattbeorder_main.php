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

        $oEvidenceList = oeVATTBEOrderEvidenceList::createOrderEvidenceList();
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
