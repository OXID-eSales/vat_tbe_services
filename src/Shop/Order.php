<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Shop;

use OxidEsales\Eshop\Application\Model\Basket as EShopBasket;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Application\Model\Order as EShopOrder;
use OxidEsales\Eshop\Application\Model\User as EShopUser;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\OrderArticleChecker;
use OxidEsales\EVatModule\Model\OrderEvidenceList;
use OxidEsales\EVatModule\Model\User;
use OxidEsales\EVatModule\Traits\ServiceContainer;

/**
 * VAT TBE User class
 */
class Order extends Order_parent
{
    use ServiceContainer;

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
     * @param EShopBasket|Basket $oBasket basket object
     * @param EShopUser|User     $oUser   order user
     *
     * @return null
     */
    public function validateOrder($oBasket, $oUser)
    {
        $iValidState = $this->getValidateOrderParent($oBasket, $oUser);
        $oUserCountry = $this->getServiceFromContainer(User::class);

        $blUserCountryChanged = $oBasket->getOeVATTBETbeCountryId() != $oUserCountry->getOeVATTBETbeCountryId();
        if (!$iValidState && $blUserCountryChanged) {
            $iValidState = EShopOrder::ORDER_STATE_INVALIDDELADDRESSCHANGED;
        }

        $oArticleChecker = $this->getOeVATTBEOrderArticleChecker($oBasket);

        $blUserFromDomesticCountry = $oUserCountry->isUserFromDomesticCountry();
        $blOrderValid = !$oBasket->hasOeTBEVATArticles() || ($oArticleChecker->isValid() && $oUserCountry->getOeVATTBETbeCountryId());

        if (!$iValidState && !$blUserFromDomesticCountry && !$blOrderValid) {
            $iValidState = Order::ORDER_STATE_TBE_NOT_CONFIGURED;
        }

        return $iValidState;
    }

    /**
     * Updates stock information, deletes current ordering details from DB,
     * returns true on success.
     * Also deletes order evidences
     *
     * @param string $sOxId Ordering ID (default null)
     *
     * @return bool
     */
    public function delete($sOxId = null)
    {
        $blSuccess = parent::delete($sOxId);

        if ($blSuccess) {
            $oOrderEvidenceList = $this->getServiceFromContainer(OrderEvidenceList::class);
            $oOrderEvidenceList->delete($sOxId ? $sOxId : $this->getId());
        }

        return $blSuccess;
    }

    /**
     * After order finalization saves used evidences to database.
     *
     * @param EShopBasket              $oBasket              Shopping basket object
     * @param EShopUser|User $oUser                Current user object
     * @param bool                  $blRecalculatingOrder Order recalculation
     *
     * @return integer
     */
    public function finalizeOrder(EShopBasket $oBasket, $oUser, $blRecalculatingOrder = false)
    {
        if (!$blRecalculatingOrder) {
            $this->oxorder__oevattbe_evidenceused = new Field($oUser->getOeVATTBETbeEvidenceUsed());
        }

        $iRet = $this->getFinalizeOrderParent($oBasket, $oUser, $blRecalculatingOrder);

        if ($this->shouldOeVATTBEStoreEvidences($iRet, $oBasket, $blRecalculatingOrder)) {
            $oOrderEvidenceList = $this->getServiceFromContainer(OrderEvidenceList::class);

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
        $oOrderEvidenceList = $this->factoryOeVATTBEOrderEvidenceList();
        $oOrderEvidenceList->load($this->getId());
        $aOrderEvidences = $oOrderEvidenceList->getData();

        $sCountryId = $aOrderEvidences[$this->getOeVATTBEUsedEvidenceId()]['countryId'];

        /** @var Country $oCountry */
        $oCountry = oxNew(Country::class);
        $oCountry->setLanguage(Registry::getLang()->getBaseLanguage());
        $oCountry->load($sCountryId);
        $sCountryTitle = $oCountry->getFieldData('oxtitle');

        return $sCountryTitle;
    }

    /**
     * Returns current order evidence id.
     *
     * @return string
     */
    protected function getOeVATTBEUsedEvidenceId()
    {
        return $this->getFieldData('oevattbe_evidenceused');
    }

    /**
     * TODO: oxPdf need adjusting since it does not exists
     * Overrides Invoice PDF module method and adds mark near TBE service VAT.
     *
     * @param oxPdf $oPdf        pdf document object
     * @param int   $iStartPos   text start position from top
     * @param bool  $blShowPrice show articles prices / VAT info or not
     */
    protected function setOrderArticlesToPdf($oPdf, &$iStartPos, $blShowPrice = true)
    {
        $iStartPosForMark = $iStartPos;
        parent::setOrderArticlesToPdf($oPdf, $iStartPos, $blShowPrice);

        $iCurrentPage = 1;
        $oPdf->setPage($iCurrentPage);

        if (!$this->_oArticles) {
            $this->_oArticles = $this->getOrderArticles(true);
        }

        // product list
        foreach ($this->_oArticles as $key => $orderProduct) {
            // starting a new page ...
            if ($iStartPosForMark > 243) {
                $iStartPosForMark = 56;
                $oPdf->setPage(++$iCurrentPage);
            } else {
                $iStartPosForMark = $iStartPosForMark + 4;
            }

            if ($blShowPrice) {
                // Add mark for TBE service.
                if ($orderProduct->getArticle()->isOeVATTBETBEService()) {
                    $oPdf->text(140, $iStartPosForMark, '*');
                    $this->setOeVATTBEHasOrderTBEServicesInInvoice(true);
                }
            }
            // additional variant info
            if ($orderProduct->getFieldData('oxselvariant')) {
                $iStartPosForMark = $iStartPosForMark + 4;
            }
        }
        if ($this->getOeVATTBEHasOrderTBEServicesInInvoice()) {
            $iStartPos += 5;
            $sCountryTitle = $this->getOeVATTBECountryTitle();
            $oPdf->text(15, $iStartPos, '* ' . sprintf(Registry::getLang()->translateString('OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY_INVOICE', $this->getSelectedLang()), $sCountryTitle));
        }
    }

    /**
     * Calls validateOrder() method on parent class and returns it's value.
     *
     * @param Basket $oBasket Basket
     * @param User   $oUser   User
     *
     * @return mixed
     */
    protected function getValidateOrderParent($oBasket, $oUser)
    {
        return parent::validateOrder($oBasket, $oUser);
    }

    /**
     * Calls finalizeOrder() method on parent class and returns it's value.
     *
     * @param Basket $oBasket              Basket
     * @param User   $oUser                User
     * @param bool     $blRecalculatingOrder Recalculation Order
     *
     * @return mixed
     */
    protected function getFinalizeOrderParent(Basket $oBasket, $oUser, $blRecalculatingOrder = false)
    {
        return parent::finalizeOrder($oBasket, $oUser, $blRecalculatingOrder);
    }

    /**
     * Returns OrderEvidenceList object.
     *
     * @return OrderEvidenceList
     */
    protected function factoryOeVATTBEOrderEvidenceList()
    {
        $oOrderEvidenceList = $this->getServiceFromContainer(OrderEvidenceList::class);
        return $oOrderEvidenceList;
    }

    /**
     * Return tbe article checker
     *
     * @param Basket $oBasket basket
     *
     * @return OrderArticleChecker
     */
    protected function getOeVATTBEOrderArticleChecker($oBasket)
    {
        return $this->getServiceFromContainer(OrderArticleChecker::class);
    }

    /**
     * Returns whether to store evidences.
     *
     * @param int                $iRet                 Order status. Check oxOrder constants for available return values.
     * @param EShopBasket|Basket $oBasket              Basket object. Will check for TBE articles inside basket.
     * @param bool               $blRecalculatingOrder Whether order recalculation is being done.
     *
     * @return bool
     */
    private function shouldOeVATTBEStoreEvidences($iRet, $oBasket, $blRecalculatingOrder)
    {
        $blCorrectOrderState = $iRet === EShopOrder::ORDER_STATE_OK || $iRet === EShopOrder::ORDER_STATE_MAILINGERROR;

        return !$blRecalculatingOrder && $blCorrectOrderState && $oBasket->hasOeTBEVATArticles();
    }
}
