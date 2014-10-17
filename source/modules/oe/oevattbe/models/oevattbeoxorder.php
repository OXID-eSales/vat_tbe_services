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
 * VAT TBE oxUser class
 */
class oeVATTBEOxOrder extends oeVATTBEOxOrder_parent
{
    /**
     * Protection parameters used for some data in order are invalid
     *
     * @var int
     */
    const ORDER_STATE_TBE_NOT_CONFIGURED = 1;

    /**
     * Validates order parameters like stock, delivery and payment
     * parameters
     *
     * @param oxbasket $oBasket basket object
     * @param oxuser   $oUser   order user
     *
     * @return null
     */
    public function validateOrder($oBasket, $oUser)
    {
        $iValidState = $this->_getValidateOrderParent($oBasket, $oUser);

        if (!$iValidState && $oUser->getTBECountryId() && ($oBasket->getTBECountryId() != $oUser->getTBECountryId())) {
            $iValidState = oxOrder::ORDER_STATE_INVALIDDElADDRESSCHANGED;
        }

        $oVATTBEOrderArticleChecker = $this->_getOeVATTBEOrderArticleChecker($oBasket);

        if (!$iValidState && !$oVATTBEOrderArticleChecker->isValid()) {
            $iValidState = oeVATTBEOxOrder::ORDER_STATE_TBE_NOT_CONFIGURED;
        }

        return $iValidState;
    }

    /**
     * Delete order together with PayPal order data.
     *
     * @param string $sOxId
     *
     * @return null
     */
    public function delete($sOxId = null)
    {
        $oOrderEvidenceList = $this->_factoryOeVATTBEOrderEvidenceList();
        $oOrderEvidenceList->delete($sOxId ? $sOxId : $this->getId());

        parent::delete($sOxId);
    }

    /**
     * After order finalization saves used evidences to database.
     *
     * @param oxBasket $oBasket              Shopping basket object
     * @param oxUser   $oUser                Current user object
     * @param bool     $blRecalculatingOrder Order recalculation
     *
     * @return integer
     */
    public function finalizeOrder(oxBasket $oBasket, $oUser, $blRecalculatingOrder = false)
    {
        $iRet = $this->_getFinalizeOrderParent($oBasket, $oUser, $blRecalculatingOrder);

        if (!$blRecalculatingOrder && $iRet == parent::ORDER_STATE_OK) {
            $oOrderEvidenceList = $this->_factoryOeVATTBEOrderEvidenceList();

            $oOrderEvidenceList->setId($this->getId());
            $aEvidenceList = $oUser->getTBEEvidenceList();
            $oOrderEvidenceList->setData($aEvidenceList);

            $oOrderEvidenceList->save();
        }

        return $iRet;
    }

    /**
     * Adds flag whether this order has TBE articles in it.
     *
     * @param oxBasket $oBasket Shopping basket object
     */
    protected function _loadFromBasket(oxBasket $oBasket)
    {
        $this->oxorder__oevattbe_hastbeservices = new oxField($oBasket->hasVATTBEArticles());
        parent::_loadFromBasket($oBasket);
    }

    /**
     * Sets default evidence used for deciding user country.
     *
     * @param oxUser $oUser user object
     */
    protected function _setUser($oUser)
    {
        $this->oxorder__oevattbe_evidenceused = new oxField($oUser->getTbeEvidenceUsed());
        parent::_setUser($oUser);
    }

    /**
     * Calls validateOrder() method on parent class and returns it's value.
     *
     * @param oxBasket $oBasket
     * @param oxUser   $oUser
     *
     * @return mixed
     */
    protected function _getValidateOrderParent($oBasket, $oUser)
    {
        return parent::validateOrder($oBasket, $oUser);
    }

    /**
     * Calls finalizeOrder() method on parent class and returns it's value.
     *
     * @param oxBasket $oBasket
     * @param oxUser   $oUser
     * @param bool     $blRecalculatingOrder
     *
     * @return mixed
     */
    protected function _getFinalizeOrderParent(oxBasket $oBasket, $oUser, $blRecalculatingOrder = false)
    {
        return parent::finalizeOrder($oBasket, $oUser, $blRecalculatingOrder);
    }

    /**
     * Returns oeVATTBEOrderEvidenceList object.
     *
     * @return oeVATTBEOrderEvidenceList
     */
    protected function _factoryOeVATTBEOrderEvidenceList()
    {
        /** @var oeVATTBEOrderEvidenceListDbGateway $oGateway */
        $oGateway = oxNew('oeVATTBEOrderEvidenceListDbGateway');

        /** @var oeVATTBEOrderEvidenceList $oOrderEvidenceList */
        $oOrderEvidenceList = oxNew('oeVATTBEOrderEvidenceList', $oGateway);

        return $oOrderEvidenceList;
    }

    /**
     * Return tbe article checker
     *
     * @param oxBasket $oBasket basket
     *
     * @return oeVATTBEOrderArticleChecker
     */
    protected function _getOeVATTBEOrderArticleChecker($oBasket)
    {
        return oxNew('oeVATTBEOrderArticleChecker', $oBasket->getBasketArticles());
    }

}
