<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
    public static function createInstance()
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
