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
        $oOrder = oxNew("oxOrder");
        $sOrderEvidenceId = $this->_getCurrentOrderEvidenceId($oOrder, $sOrderId);

        /** @var oeVATTBEOrderEvidenceListDbGateway $oDbGateway */
        $oDbGateway = oxNew('oeVATTBEOrderEvidenceListDbGateway');
        $aOrder = $oDbGateway->load($sOrderId);

        /** @var oxCountry $oCountry */
        $oCountry = oxNew('oxCountry');
        $sTBECountryTitle = $this->_getTBECountryTitle($oCountry, $aOrder[$sOrderEvidenceId]['countryId']);
        $aEvidencesData = $this->_prepareEvidencesData($aOrder, $oCountry);

        $this->_aViewData["sTBECountry"] = $sTBECountryTitle;
        $this->_aViewData["aEvidencesData"] = $aEvidencesData;

        return parent::render();
    }

    /**
     * Prepares data for template.
     *
     * @param array     $aOrder   Currently selected order evidences data.
     * @param oxCountry $oCountry Object used to load country title.
     *
     * @return array
     */
    protected function _prepareEvidencesData($aOrder, $oCountry)
    {
        foreach ($aOrder as $sEvidenceId => $aOrderInfo) {
            if ($oCountry->load($aOrderInfo['countryId'])) {
                $aOrder[$sEvidenceId]['countryTitle'] = $oCountry->oxcountry__oxtitle->value;
            } else {
                $aOrder[$sEvidenceId]['countryTitle'] = '-';
            }
        }

        return $aOrder;
    }

    /**
     * Returns TBE country title.
     *
     * @param oxCountry $oCountry      Object used to load country title.
     * @param string    $sTBECountryId Country id.
     *
     * @return string
     */
    protected function _getTBECountryTitle($oCountry, $sTBECountryId)
    {
        $oCountry->load($sTBECountryId);
        $sTBECountryTitle = $oCountry->oxcountry__oxtitle->value;

        return $sTBECountryTitle;
    }

    /**
     * Returns currently selected order evidence id.
     *
     * @param oxOrder $oOrder   Used to get evidence id.
     * @param string  $sOrderId Order id.
     *
     * @return string
     */
    protected function _getCurrentOrderEvidenceId($oOrder, $sOrderId)
    {
        $oOrder->load($sOrderId);
        $sEvidenceId = $oOrder->oxorder__oevattbe_evidenceused->value;

        return $sEvidenceId;
    }
}
