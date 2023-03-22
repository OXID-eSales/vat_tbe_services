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
