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

namespace OxidEsales\EVatModule\Model;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\EVatModule\Model\DbGateway\oeVATTBECategoryVATGroupsPopulatorDbGateway;
use OxidEsales\EVatModule\Shop\oeVATTBEOxCategory;

/**
 * VAT Groups handling class
 */
class oeVATTBECategoryArticlesUpdater
{
    /**
     * Handles class dependencies.
     *
     * @param oeVATTBECategoryVATGroupsPopulatorDbGateway $oDbGateway db gateway
     */
    public function __construct(oeVATTBECategoryVATGroupsPopulatorDbGateway $oDbGateway)
    {
        $this->_oDbGateway = $oDbGateway;
    }

    /**
     * Creates an instance of oeVATTBECategoryArticlesUpdater.
     *
     * @return oeVATTBECategoryArticlesUpdater
     */
    public static function createInstance()
    {
        $oGateway = oxNew(oeVATTBECategoryVATGroupsPopulatorDbGateway::class);
        $oList = oxNew(oeVATTBECategoryArticlesUpdater::class, $oGateway);

        return $oList;
    }

    /**
     * Method for model saving (insert and update data).
     *
     * @param Category|oeVATTBEOxCategory $oCategory category
     */
    public function addCategoryTBEInformationToArticles($oCategory)
    {
        $this->_getDbGateway()->populate($oCategory->getId());
    }

    /**
     * Resets articles to be not TBE services.
     *
     * @param array $aArticles
     */
    public function removeCategoryTBEInformationFromArticles($aArticles)
    {
        $this->_getDbGateway()->reset($aArticles);
    }

    /**
     * Returns model database gateway.
     *
     * @return oeVATTBECategoryVATGroupsPopulatorDbGateway
     */
    protected function _getDbGateway()
    {
        return $this->_oDbGateway;
    }
}
