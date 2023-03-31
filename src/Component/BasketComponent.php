<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Component;

use OxidEsales\Eshop\Application\Model\Basket as EShopBasket;
use OxidEsales\Eshop\Application\Model\User as EShopUser;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Shop\Basket;
use OxidEsales\EVatModule\Shop\User;

/**
 * VAT TBE oxBasket class
 */
class BasketComponent extends BasketComponent_parent
{
    /**
     * Loads basket ($oBasket = $mySession->getBasket()), calls oBasket->calculateBasket,
     * executes parent::render() and returns basket object.
     *
     * @return object $oBasket Basket object.
     */
    public function render()
    {
        /** @var EShopBasket|Basket $oBasket */
        $oBasket = Registry::getSession()->getBasket();
        if ($oBasket) {
            /** @var EShopUser|User $oUser */
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
