<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * VAT TBE oxUser class
 */
class oeVATTBEOxVatSelector extends oeVATTBEOxVatSelector_parent
{
    /**
     * get article user vat
     *
     * @param oeVatTbeOxArticle|oxArticle $oArticle Article object.
     *
     * @return double
     */
    public function getArticleUserVat(oxArticle $oArticle)
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
     * @param oeVatTbeOxArticle|oxArticle $oArticle Article object.
     *
     * @return bool
     */
    private function _oeVATTBEUseTBEVAT($oArticle)
    {
        $oUserCountry = oeVATTBETBEUser::createInstance();

        $blIsTBEArticle = $oArticle->isOeVATTBETBEService() && $oArticle->getOeVATTBETBEVat() !== null;

        return $blIsTBEArticle && !$oUserCountry->isUserFromDomesticCountry() && !$this->isAdmin();
    }
}
