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
 * VAT TBE oxBasket class
 */
class oeVATTBEOxCmp_Basket extends oeVatTbeOxCmp_Basket_parent
{
    /**
     * Loads basket ($oBasket = $mySession->getBasket()), calls oBasket->calculateBasket,
     * executes parent::render() and returns basket object.
     *
     * @return object   $oBasket    basket object
     */
    public function render()
    {
        if ($oBasket = $this->getSession()->getBasket()) {
            $oUser = $this->getUser();
            if ($oUser) {
                if ((is_null($oBasket->getTbeCountryId()) || ($oBasket->getTbeCountryId() != $oUser->getTbeCountryId()))) {
                    if ($oBasket->hasVATTBEArticles()) {
                        $oBasket->setTBECountryChanged();
                    }
                    $oBasket->setTBECountryId($oUser->getTbeCountryId());
                    $oBasket->calculateBasket(true);
                }
            } else {
                $oBasket->calculateBasket(false);
            }
        }

        parent::render();

        return $oBasket;
    }
}
