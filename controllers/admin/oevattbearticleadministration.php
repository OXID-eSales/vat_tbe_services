<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Class responsible for TBE service administration.
 */
class oeVATTBEArticleAdministration extends oxAdminDetails
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

        $oConfig = $this->getConfig();
        $oArticle = $this->_loadCurrentArticle();
        if ('EE' == $oConfig->getEdition() && $oArticle->isDerived()) {
            $this->_aViewData['readonly'] = true;
        }

        return 'oevattbearticleadministration.tpl';
    }

    /**
     * Updates article information related with TBE services.
     */
    public function save()
    {
        parent::save();
        $sCurrentArticleId = $this->getEditObjectId();
        $oConfig = $this->getConfig();
        $aParams = $oConfig->getRequestParameter('editval');
        $aVATGroupsParams = $oConfig->getRequestParameter('VATGroupsByCountry');
        $oArticleVATGroupsList = oeVATTBEArticleVATGroupsList::createInstance();
        $oArticleVATGroupsList->setId($sCurrentArticleId);
        $oArticleVATGroupsList->setData($aVATGroupsParams);
        $oArticleVATGroupsList->save();

        $oArticle = $this->_loadCurrentArticle();
        $oArticle->oxarticles__oevattbe_istbeservice = new oxField($aParams['oevattbe_istbeservice']);
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
        $oArticleVATGroupsList = oeVATTBEArticleVATGroupsList::createInstance();
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
        /** @var oxCountry|oeVATTBEoxCountry $oCountry */
        $oCountry = oxNew('oxCountry');
        $aViewData = array();
        $oCountryVATGroupsList = oeVATTBECountryVATGroupsList::createInstance();
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
        /** @var oxCategory $oArticle */
        $oArticle = oxNew('oxArticle');
        $sCurrentArticleId = $this->getEditObjectId();
        $oArticle->load($sCurrentArticleId);

        return (int)$oArticle->oxarticles__oevattbe_istbeservice->value;
    }

    /**
     * Load current article object.
     *
     * @return oeVATTBEOxArticle|oxArticle
     */
    protected function _loadCurrentArticle()
    {
        $sCurrentArticleId = $this->getEditObjectId();
        /** @var oeVATTBEOxArticle|oxArticle $oArticle */
        $oArticle = oxNew('oxArticle');
        $oArticle->load($sCurrentArticleId);

        return $oArticle;
    }
}
