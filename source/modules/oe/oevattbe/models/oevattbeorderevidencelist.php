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
 * Order Evidence list class.
 */
class oeVATTBEOrderEvidenceList extends oeVATTBEModel
{
    /** @var array Model data. */
    protected $_aData = array();

    /**
     * Creates an instance of oeVATTBEOrderEvidenceList.
     *
     * @return oeVATTBEOrderEvidenceList;
     */
    public static function createOrderEvidenceList()
    {
        /** @var oeVATTBEOrderEvidenceListDbGateway $oGateway */
        $oGateway = oxNew('oeVATTBEOrderEvidenceListDbGateway');
        /** @var oeVATTBEOrderEvidenceList $oList */
        $oList = oxNew('oeVATTBEOrderEvidenceList', $oGateway);

        return $oList;
    }

    /**
     * Method for model saving (insert and update data).
     *
     * @return int|false
     */
    public function save()
    {
        $aData = array(
            'evidenceList' => $this->getData(),
            'orderId' => $this->getId()
        );
        $this->_getDbGateway()->save($aData);

        return $this->getId();
    }

    /**
     * Loads evidence data with country title.
     *
     * @param string|null $sId Order evidence id.
     */
    public function loadWithCountryNames($sId = null)
    {
        /** @var oxCountry $oCountry */
        $oCountry = oxNew('oxCountry');
        $this->load($sId);
        $aData = $this->getData();
        foreach ($aData as $sEvidenceId => $aOrderInfo) {
            if ($oCountry->load($aOrderInfo['countryId'])) {
                $aData[$sEvidenceId]['countryTitle'] = $oCountry->oxcountry__oxtitle->value;
            } else {
                $aData[$sEvidenceId]['countryTitle'] = '-';
            }
        }
        $this->setData($aData);
    }
}
