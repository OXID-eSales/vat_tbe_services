<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Extended oxViewConfig class
 */
class oeVATTBEOxViewConfig extends oeVATTBEOxViewConfig_parent
{

    /**
     * Returns whether to show notice starts for given article.
     *
     * @param oxArticle|oeVATTBEOxArticle $oArticle oxArticle object to check.
     *
     * @return bool
     */
    public function oeVATTBEShowTBEArticlePriceNotice($oArticle)
    {
        $oTBEUserCountry = oeVATTBETBEUser::createInstance();

        return $oArticle->isOeVATTBETBEService() && !$oTBEUserCountry->isUserFromDomesticCountry();
    }

    /**
     * Return shop edition (EE|CE|PE)
     * Wrapper to get Shop edition.
     * Is needed for shop versions lower then 5.2.
     *
     * @return string
     */
    public function getOeVATTBEShowTBEEdition()
    {
        return $this->getConfig()->getEdition();
    }

    /**
     * Check if currently active theme is based on flow.
     *
     * @return bool
     */
    public function isActiveThemeBasedOnFlow()
    {
        return (bool) ('flow' == $this->getActiveTheme()) || ('flow' == $this->getParentThemeId());
    }

    /**
     * Getter for parent theme id.
     *
     * @return string
     */
    protected function getParentThemeId()
    {
        $parentThemeId = '';
        $themeName = $this->getActiveTheme();
        $theme = oxNew(\OxidEsales\Eshop\Core\Theme::class);
        $theme->load($themeName);
        if ($parentTheme = $theme->getParent()) {
            $parentThemeId = $parentTheme->getId();
        }
        return $parentThemeId;
    }
}
