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
 * VAT TBE oxUser class
 */
class oeVATTBEOxOrder extends oeVATTBEOxOrder_parent
{
    /**
     * Protection parameters used for some data in order are invalid
     *
     * @var int
     */
    const ORDER_STATE_TBE_NOT_CONFIGURED = 10;

    /** @var bool If order has TBE services. */
    private $_blHasOrderTBEServicesInInvoice;

    /**
     * Returns if order has TBE services.
     *
     * @return boolean
     */
    public function getOeVATTBEHasOrderTBEServicesInInvoice()
    {
        return (bool)$this->_blHasOrderTBEServicesInInvoice;
    }

    /**
     * Sets if order has TBE services.
     *
     * @param boolean $blHasOrderTBEServicesInInvoice
     */
    public function setOeVATTBEHasOrderTBEServicesInInvoice($blHasOrderTBEServicesInInvoice)
    {
        $this->_blHasOrderTBEServicesInInvoice = $blHasOrderTBEServicesInInvoice;
    }

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

        if (!$iValidState && $oUser->getOeVATTBETbeCountryId() && ($oBasket->getOeVATTBETbeCountryId() != $oUser->getOeVATTBETbeCountryId())) {
            $iValidState = oxOrder::ORDER_STATE_INVALIDDElADDRESSCHANGED;
        }

        $oVATTBEOrderArticleChecker = $this->_getOeVATTBEOrderArticleChecker($oBasket);

        if (!$iValidState && !$oVATTBEOrderArticleChecker->isValid()) {
            $iValidState = oeVATTBEOxOrder::ORDER_STATE_TBE_NOT_CONFIGURED;
        }
        return $iValidState;
    }

    /**
     * Delete order.
     *
     * @param string $sOxId id
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
     * @param oxBasket              $oBasket              Shopping basket object
     * @param oxUser|oeVATTBEOxUser $oUser                Current user object
     * @param bool                  $blRecalculatingOrder Order recalculation
     *
     * @return integer
     */
    public function finalizeOrder(oxBasket $oBasket, $oUser, $blRecalculatingOrder = false)
    {
        $iRet = $this->_getFinalizeOrderParent($oBasket, $oUser, $blRecalculatingOrder);

        if ($this->_shouldOeVATTBEStoreEvidences($iRet, $oBasket, $blRecalculatingOrder)) {
            $oOrderEvidenceList = $this->_factoryOeVATTBEOrderEvidenceList();

            $oOrderEvidenceList->setId($this->getId());
            $aEvidenceList = $oUser->getOeVATTBEEvidenceList();
            $oOrderEvidenceList->setData($aEvidenceList);

            $oOrderEvidenceList->save();
        }

        return $iRet;
    }

    /**
     * Returns order residence country from evidence.
     *
     * @return string
     */
    public function getOeVATTBECountryTitle()
    {
        $oOrderEvidenceList = $this->_factoryOeVATTBEOrderEvidenceList();
        $oOrderEvidenceList->load($this->getId());
        $aOrderEvidences = $oOrderEvidenceList->getData();

        $sCountryId = $aOrderEvidences[$this->_oeVATTBEGetUsedEvidenceId()]['countryId'];

        /** @var oxCountry $oCountry */
        $oCountry = oxNew('oxCountry');
        $oCountry->setLanguage($this->getSelectedLang());
        $oCountry->load($sCountryId);
        $sCountryTitle = $oCountry->oxcountry__oxtitle->value;

        return $sCountryTitle;
    }

    /**
     * Returns current order evidence id.
     *
     * @return string
     */
    protected function _oeVATTBEGetUsedEvidenceId()
    {
        return $this->oxorder__oevattbe_evidenceused->value;
    }

    /**
     * Overrides Invoice PDF module method and adds mark near TBE service VAT.
     *
     * @param oxPdf $oPdf        pdf document object
     * @param int   $iStartPos   text start position from top
     * @param bool  $blShowPrice show articles prices / VAT info or not
     */
    protected function _setOrderArticlesToPdf($oPdf, &$iStartPos, $blShowPrice = true)
    {
        $iStartPosForMark = $iStartPos;
        parent::_setOrderArticlesToPdf($oPdf, $iStartPos, $blShowPrice);

        $iCurrentPage = 1;
        $oPdf->setPage($iCurrentPage);

        if (!$this->_oArticles) {
            $this->_oArticles = $this->getOrderArticles(true);
        }

        // product list
        foreach ($this->_oArticles as $key => $oOrderArt) {
            // starting a new page ...
            if ($iStartPosForMark > 243) {
                $iStartPosForMark = 56;
                $oPdf->setPage(++$iCurrentPage);
            } else {
                $iStartPosForMark = $iStartPosForMark + 4;
            }

            if ($blShowPrice) {
                // Add mark for TBE service.
                if ($oOrderArt->getArticle()->isOeVATTBETBEService()) {
                    $oPdf->text(140, $iStartPosForMark, '*');
                    $this->setOeVATTBEHasOrderTBEServicesInInvoice(true);
                }
            }
            // additional variant info
            if ($oOrderArt->oxorderarticles__oxselvariant->value) {
                $iStartPosForMark = $iStartPosForMark + 4;
            }
        }
        if ($this->getOeVATTBEHasOrderTBEServicesInInvoice()) {
            $iStartPos += 5;
            $sCountryTitle = $this->getOeVATTBECountryTitle();
            $oPdf->text(15, $iStartPos, '* ' . sprintf(oxRegistry::getLang()->translateString('OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY_INVOICE', $this->getSelectedLang()), $sCountryTitle));
        }
    }

    /**
     * Adds flag whether this order has TBE articles in it.
     *
     * @param oxBasket $oBasket Shopping basket object
     */
    protected function _loadFromBasket(oxBasket $oBasket)
    {
        $this->oxorder__oevattbe_hastbeservices = new oxField($oBasket->hasOeTBEVATArticles());
        parent::_loadFromBasket($oBasket);
    }

    /**
     * Sets default evidence used for deciding user country.
     *
     * @param oxUser|oeVATTBEOxUser $oUser user object
     */
    protected function _setUser($oUser)
    {
        $this->oxorder__oevattbe_evidenceused = new oxField($oUser->getOeVATTBETbeEvidenceUsed());
        parent::_setUser($oUser);
    }

    /**
     * Calls validateOrder() method on parent class and returns it's value.
     *
     * @param oxBasket $oBasket Basket
     * @param oxUser   $oUser   User
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
     * @param oxBasket $oBasket              Basket
     * @param oxUser   $oUser                User
     * @param bool     $blRecalculatingOrder Recalculation Order
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
        $oOrderEvidenceList = oeVATTBEOrderEvidenceList::createOrderEvidenceList();
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
        $oTBEUser  = oxNew('oeVATTBETBEUser', oxNew('oxUser'), oxRegistry::getSession(), oxRegistry::getConfig());
        return oxNew('oeVATTBEOrderArticleChecker', $oBasket->getBasketArticles(), $oTBEUser);
    }

    /**
     * Returns whether to store evidences.
     *
     * @param int                     $iRet                 Order status. Check oxOrder constants for available return values.
     * @param oxBasket|oeVATTBEOxUser $oBasket              Basket object. Will check for TBE articles inside basket.
     * @param bool                    $blRecalculatingOrder Whether order recalculation is being done.
     *
     * @return bool
     */
    private function _shouldOeVATTBEStoreEvidences($iRet, $oBasket, $blRecalculatingOrder)
    {
        $blCorrectOrderState = $iRet === oxOrder::ORDER_STATE_OK || $iRet === oxOrder::ORDER_STATE_MAILINGERROR;

        return !$blRecalculatingOrder && $blCorrectOrderState && $oBasket->hasOeTBEVATArticles();
    }
}
