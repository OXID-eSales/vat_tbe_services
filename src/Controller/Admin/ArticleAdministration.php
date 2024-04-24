<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Application\Model\Article as EShopArticle;
use OxidEsales\Eshop\Application\Model\Country as EShopCountry;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EVatModule\Model\ArticleVATGroupsList;
use OxidEsales\EVatModule\Model\CountryVATGroupsList;
use OxidEsales\EVatModule\Shop\Article;
use OxidEsales\EVatModule\Shop\Country;
use OxidEsales\Facts\Facts;

/**
 * Class responsible for TBE service administration.
 */
class ArticleAdministration extends AdminDetailsController
{
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

        $article = $this->loadCurrentArticle();
        if ('EE' == (new Facts())->getEdition() && $article->isDerived()) {
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
        $articleVATGroupsList = ContainerFacade::get(ArticleVATGroupsList::class);
        $articleVATGroupsList->setId($sCurrentArticleId);
        $articleVATGroupsList->setData($aVATGroupsParams);
        $articleVATGroupsList->save();

        $article = $this->loadCurrentArticle();
        $article->oxarticles__oevattbe_istbeservice = new Field($aParams['oevattbe_istbeservice']);
        $article->save();
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
        $articleVATGroupsList = ContainerFacade::get(ArticleVATGroupsList::class);
        $articleVATGroupsList->load($this->getEditObjectId());
        if (is_null($this->_aArticleVATGroupData)) {
            $this->_aArticleVATGroupData = $articleVATGroupsList->getData();
        }

        if (!isset($this->_aArticleVATGroupData[$sCountryId])) {
            return false;
        }

        return (int)$this->_aArticleVATGroupData[$sCountryId] === (int)$sVATGroupId;
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
        $oCountryVATGroupsList = ContainerFacade::get(CountryVATGroupsList::class);
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
        /** @var EShopArticle $article */
        $article = oxNew(EShopArticle::class);
        $sCurrentArticleId = $this->getEditObjectId();
        $article->load($sCurrentArticleId);

        return (int) $article->getFieldData('oevattbe_istbeservice');
    }

    /**
     * Load current article object.
     *
     * @return EShopArticle|Article
     */
    protected function loadCurrentArticle()
    {
        $sCurrentArticleId = $this->getEditObjectId();
        /** @var EShopArticle|Article $article */
        $article = oxNew(EShopArticle::class);
        $article->load($sCurrentArticleId);

        return $article;
    }
}
