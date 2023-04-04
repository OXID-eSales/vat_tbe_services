<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model;

use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\EVatModule\Core\Model;
use OxidEsales\EVatModule\Model\DbGateway\OrderEvidenceListDbGateway;

/**
 * Order Evidence list class.
 */
class OrderEvidenceList extends Model
{
    /** @var array Model data. */
    protected $_aData = array();

    public function __construct(
        protected OrderEvidenceListDbGateway $_oDbGateway
    )
    {
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
        $this->getDbGateway()->save($aData);

        return $this->getId();
    }

    /**
     * Loads evidence data with country title.
     *
     * @param string|null $sId Order evidence id.
     */
    public function loadWithCountryNames($sId = null)
    {
        /** @var Country $country */
        $country = oxNew(Country::class);
        $this->load($sId);
        $aData = $this->getData();
        foreach ($aData as $sEvidenceId => $aOrderInfo) {
            if ($country->load($aOrderInfo['countryId'])) {
                $aData[$sEvidenceId]['countryTitle'] = $country->getFieldData('oxtitle');
            } else {
                $aData[$sEvidenceId]['countryTitle'] = '-';
            }
        }
        $this->setData($aData);
    }
}
