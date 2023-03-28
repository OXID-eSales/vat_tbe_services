<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * VAT TBE oxBasket class
 */
class oeVATTBEOxCmp_Basket extends oeVatTbeOxCmp_Basket_parent
{
    /**
     * Loads basket ($oBasket = $mySession->getBasket()), calls oBasket->calculateBasket,
     * executes parent::render() and returns basket object.
     *
     * @return object $oBasket Basket object.
     */
    public function render()
    {
        /** @var oxBasket|oeVATTBEOxBasket $oBasket */
        $oBasket = $this->getSession()->getBasket();
        if ($oBasket) {
            /** @var oxUser|oeVATTBEOxUser $oUser */
            $oUser = $this->getUser();
            if ($oUser) {
                $sUserCountryId = $oUser->getOeVATTBETbeCountryId();
                $oBasket->setOeVATTBECountryId($sUserCountryId);
            } else {
                $oBasket->calculateBasket(false);
            }
        }

        parent::render();

        return $oBasket;
    }
}
