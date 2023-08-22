<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Shop;

use OxidEsales\Eshop\Application\Model\Article as EShopArticle;
use OxidEsales\EVatModule\Model\User;
use OxidEsales\EVatModule\Traits\ServiceContainer;
use OxidEsales\Facts\Facts;

/**
 * Extended oxViewConfig class
 */
class ViewConfig extends ViewConfig_parent
{
    use ServiceContainer;

    /**
     * Returns whether to show notice starts for given article.
     *
     * @param EShopArticle|Article $oArticle oxArticle object to check.
     *
     * @return bool
     */
    public function oeVATTBEShowTBEArticlePriceNotice($oArticle)
    {
        $oTBEUserCountry = $this->getServiceFromContainer(User::class);

        return $oArticle->isOeVATTBETBEService() && !$oTBEUserCountry->isUserFromDomesticCountry();
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
