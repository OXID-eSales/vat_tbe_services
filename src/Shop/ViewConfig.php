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
