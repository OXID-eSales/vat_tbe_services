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
 * @copyright (C) OXID eSales AG 2003-2014
 */

/**
 * PayPal PayPalOrder class
 */
class oeVATTBEOrderEvidenceList extends oeVATTBEModel
{
    /** @var string Order ID for which evidence list is loaded.  */
    private $_sOrderId = null;

    /**
     * Sets order id.
     *
     * @param string $sOrderId
     */
    public function setId($sOrderId)
    {
        $this->_sOrderId = $sOrderId;
    }

    /**
     * Returns order id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->_sOrderId;
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
     * Return database gateway.
     *
     * @return oeVATTBEOrderEvidenceListDbGateway
     */
    protected function _getDbGateway()
    {
        if (is_null($this->_oDbGateway)) {
            $this->_setDbGateway(oxNew('oeVATTBEOrderEvidenceListDbGateway'));
        }

        return $this->_oDbGateway;
    }
}
