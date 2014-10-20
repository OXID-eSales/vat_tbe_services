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
        $oOrder->load($sOrderId);
        $sEvidence = $oOrder->oxorder__oevattbe_evidenceused->value;
        /** @var oeVATTBEOrderEvidenceListDbGateway $oDbGateway */
        $oDbGateway = oxNew('oeVATTBEOrderEvidenceListDbGateway');
        $aOrder = $oDbGateway->load($sOrderId);


        /** @var oxCountry $oCountry */
        $oCountry = oxNew('oxCountry');
        $sTBECountryId = $aOrder[$sEvidence]['countryId'];
        $oCountry->load($sTBECountryId);
        $sTBECountry = $oCountry->oxcountry__oxtitle->value;

        foreach ($aOrder as $sKey => $aOrderInfo) {
            if ($oCountry->load($aOrderInfo['countryId'])) {
                $aOrder[$sKey]['countryTitle'] = $oCountry->oxcountry__oxtitle->value;
            }
        }

        $this->_aViewData["sTBECountry"] = $sTBECountry;
        $this->_aViewData["aCountriesByEvidences"] = $aOrder;

        return parent::render();
    }
}
