<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\EVatModule\Model\OrderEvidenceList;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * Adds additional functionality needed for oeVATTBE module when managing orders.
 */
class OrderMain extends OrderMain_parent
{
    use ServiceContainer;

    /**
     * Returns template name from parent and sets values for template.
     *
     * @return string
     */
    public function render()
    {
        $sOrderId = $this->getEditObjectId();

        /** @var Order $oOrder */
        $oOrder = oxNew(Order::class);
        $sOrderEvidenceId = $this->getOeVATTBECurrentOrderEvidenceId($oOrder, $sOrderId);

        $oEvidenceList = $this->getServiceFromContainer(OrderEvidenceList::class);
        $oEvidenceList->loadWithCountryNames($sOrderId);
        $aEvidencesData = $oEvidenceList->getData();

        $this->addTplParam('sTBECountry', $aEvidencesData[$sOrderEvidenceId]['countryTitle']);
        $this->addTplParam('aEvidenceUsed', $sOrderEvidenceId);
        $this->addTplParam('aEvidencesData', $aEvidencesData);

        return parent::render();
    }

    /**
     * Returns currently selected order evidence id.
     *
     * @param Order $oOrder   Used to get evidence id.
     * @param string  $sOrderId Order id.
     *
     * @return string
     */
    protected function getOeVATTBECurrentOrderEvidenceId($oOrder, $sOrderId)
    {
        $oOrder->load($sOrderId);
        $sEvidenceId = $oOrder->oxorder__oevattbe_evidenceused->value;

        return $sEvidenceId;
    }
}
