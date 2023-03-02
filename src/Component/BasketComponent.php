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
