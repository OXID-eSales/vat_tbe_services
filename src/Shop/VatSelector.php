<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Shop;

use OxidEsales\Eshop\Application\Model\Article as EShopArticle;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
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
        $user = $oArticle->getArticleUser();

        if ((!$user || !$user->getFieldData('oxustid')) && $this->oeVATTBEUseTBEVAT($oArticle)) {
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
    private function oeVATTBEUseTBEVAT($oArticle)
    {
        $oUserCountry = ContainerFacade::get(User::class);

        $blIsTBEArticle = $oArticle->isOeVATTBETBEService() && $oArticle->getOeVATTBETBEVat() !== null;

        return $blIsTBEArticle && !$oUserCountry->isUserFromDomesticCountry() && !$this->isAdmin();
    }
}
