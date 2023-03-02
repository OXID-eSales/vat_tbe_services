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

namespace OxidEsales\EVatModule\Shop;

use OxidEsales\Eshop\Application\Model\Article as EShopArticle;
use OxidEsales\EVatModule\Model\User;

/**
 * VAT TBE oxUser class
 */
class VatSelector extends VatSelector_parent
{
    /**
     * get article user vat
     *
     * @param Article|EShopArticle $oArticle Article object.
     *
     * @return double
     */
    public function getArticleUserVat(EShopArticle $oArticle)
    {
        $oUser = $oArticle->getArticleUser();

        if (!$oUser->oxuser__oxustid->value && $this->_oeVATTBEUseTBEVAT($oArticle)) {
            $sVat = $oArticle->getOeVATTBETBEVat();
        } else {
            $sVat = parent::getArticleUserVat($oArticle);
        }

        return $sVat;
    }

    /**
     * Returns whether TBE VAT for given article should be used.
     *
     * @param Article|EShopArticle $oArticle Article object.
     *
     * @return bool
     */
    private function _oeVATTBEUseTBEVAT($oArticle)
    {
        $oUserCountry = User::createInstance();

        $blIsTBEArticle = $oArticle->isOeVATTBETBEService() && $oArticle->getOeVATTBETBEVat() !== null;

        return $blIsTBEArticle && !$oUserCountry->isUserFromDomesticCountry() && !$this->isAdmin();
    }
}
