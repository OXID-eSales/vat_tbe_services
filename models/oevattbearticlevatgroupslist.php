<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * VAT Groups handling class
 */
class oeVATTBEArticleVATGroupsList extends oeVATTBEModel
{
    /** @var array Model data. */
    protected $_aData = array();

    /**
     * Creates an instance of oeVATTBEArticleVATGroupsList.
     *
     * @return oeVATTBEArticleVATGroupsList;
     */
    public static function createInstance()
    {
        $oGateway = oxNew('oeVATTBEArticleVATGroupsDbGateway');
        $oList = oxNew('oeVATTBEArticleVATGroupsList', $oGateway);

        return $oList;
    }

    /**
     * Method for model saving (insert and update data).
     *
     * @return int|false
     */
    public function save()
    {
        $aData = $this->getData();
        $aDbData = array();
        foreach ($aData as $sCountryId => $sGroupId) {
            if ($sGroupId) {
                $aDbData[] = array(
                    'OEVATTBE_ARTICLEID' => $this->getId(),
                    'OEVATTBE_COUNTRYID' => $sCountryId,
                    'OEVATTBE_VATGROUPID' => $sGroupId
                );
            }
        }

        $aData = array(
            'articleid' => $this->getId(),
            'relations' => $aDbData
        );
        $this->_getDbGateway()->save($aData);

        return $this->getId();
    }

    /**
     * Method for loading article VAT group list. If loaded - returns true.
     *
     * @param string $sId Article id.
     *
     * @return bool
     */
    public function load($sId = null)
    {
        if (!is_null($sId)) {
            $this->setId($sId);
        }

        $this->_blIsLoaded = false;
        $aDbData = $this->_getDbGateway()->load($this->getId());
        if ($aDbData) {
            $aData = array();
            foreach ($aDbData as $aRecord) {
                $aData[$aRecord['OEVATTBE_COUNTRYID']] = $aRecord['OEVATTBE_VATGROUPID'];
            }
            $this->setData($aData);
            $this->_blIsLoaded = true;
        }

        return $this->isLoaded();
    }

    /**
     * Method for loading article VAT group list. If loaded - returns true.
     *
     * @param string $sGroupId Group id.
     *
     * @return array
     */
    public function getArticlesAssignedToGroup($sGroupId = null)
    {
        $aData = array();
        $aDbData = $this->_getDbGateway()->loadByGroupId($sGroupId);
        if ($aDbData) {
            foreach ($aDbData as $aRecord) {
                $aData[] = $aRecord['OEVATTBE_ARTICLEID'];
            }
        }

        return $aData;
    }
}
