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

namespace OxidEsales\EVatModule\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Application\Model\Article as EShopArticle;
use OxidEsales\Eshop\Application\Model\Country as EShopCountry;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EVatModule\Model\ArticleVATGroupsList;
use OxidEsales\EVatModule\Model\CountryVATGroupsList;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\EVatModule\Shop\Country;
use OxidEsales\EVatModule\Traits\ServiceContainer;
use OxidEsales\Facts\Facts;

/**
 * Class responsible for TBE service administration.
 */
class ArticleAdministration extends AdminDetailsController
{
    use ServiceContainer;

    /** @var array Used to cache VAT Groups data. */
    private $_aArticleVATGroupData = null;

    /**
     * Renders template for VAT TBE administration in article page.
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $oArticle = $this->loadCurrentArticle();
        if ('EE' == (new Facts())->getEdition() && $oArticle->isDerived()) {
            $this->_aViewData['readonly'] = true;
        }

        return '@oevattbe/admin/oevattbearticleadministration';
    }

    /**
     * Updates article information related with TBE services.
     */
    public function save()
    {
        parent::save();
        $sCurrentArticleId = $this->getEditObjectId();
        $request = Registry::getRequest();
        $aParams = $request->getRequestParameter('editval');
        $aVATGroupsParams = $request->getRequestParameter('VATGroupsByCountry');
        $oArticleVATGroupsList = $this->getServiceFromContainer(ArticleVATGroupsList::class);
        $oArticleVATGroupsList->setId($sCurrentArticleId);
        $oArticleVATGroupsList->setData($aVATGroupsParams);
        $oArticleVATGroupsList->save();

        $oArticle = $this->loadCurrentArticle();
        $oArticle->oxarticles__oevattbe_istbeservice = new Field($aParams['oevattbe_istbeservice']);
        $oArticle->save();
    }

    /**
     * Used in template to check if select element was selected.
     *
     * @param string $sCountryId  Html select element country.
     * @param string $sVATGroupId Group which is checked.
     *
     * @return bool
     */
    public function isSelected($sCountryId, $sVATGroupId)
    {
        $oArticleVATGroupsList = $this->getServiceFromContainer(ArticleVATGroupsList::class);
        $oArticleVATGroupsList->load($this->getEditObjectId());
        if (is_null($this->_aArticleVATGroupData)) {
            $this->_aArticleVATGroupData = $oArticleVATGroupsList->getData();
        }

        return $this->_aArticleVATGroupData[$sCountryId] === $sVATGroupId;
    }

    /**
     * Forms view VAT groups data for template.
     *
     * @return array
     */
    public function getCountryAndVATGroupsData()
    {
        /** @var EShopCountry|Country $oCountry */
        $oCountry = oxNew(EShopCountry::class);
        $aViewData = array();
        $oCountryVATGroupsList = $this->getServiceFromContainer(CountryVATGroupsList::class);
        $aVATGroupList = $oCountryVATGroupsList->getList();
        foreach ($aVATGroupList as $sCountryId => $aGroupsList) {
            $oCountry->load($sCountryId);
            $aViewData[$sCountryId] = array(
                'countryTitle' => $oCountry->getOeVATTBEName(),
                'countryGroups' => $aGroupsList
            );
        }

        return $aViewData;
    }

    /**
     * Returns if selected article is TBE service.
     *
     * @return int
     */
    public function isArticleTBE()
    {
        /** @var EShopArticle $oArticle */
        $oArticle = oxNew(EShopArticle::class);
        $sCurrentArticleId = $this->getEditObjectId();
        $oArticle->load($sCurrentArticleId);

        return (int)$oArticle->oxarticles__oevattbe_istbeservice->value;
    }

    /**
     * Load current article object.
     *
     * @return EShopArticle|Article
     */
    protected function loadCurrentArticle()
    {
        $sCurrentArticleId = $this->getEditObjectId();
        /** @var EShopArticle|Article $oArticle */
        $oArticle = oxNew(EShopArticle::class);
        $oArticle->load($sCurrentArticleId);

        return $oArticle;
    }
}
