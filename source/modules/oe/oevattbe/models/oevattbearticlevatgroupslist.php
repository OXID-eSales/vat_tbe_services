<?php
/**
 * This file is part of OXID eSales VAT TBE module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales VAT TBE module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
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
    public static function createArticleVATGroupsList()
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
            $aDbData[] = array(
                'OEVATTBE_ARTICLEID' => $this->getId(),
                'OEVATTBE_COUNTRYID' => $sCountryId,
                'OEVATTBE_VATGROUPID' => $sGroupId
            );
        }

        $aData = array(
            'articleid' => $this->getId(),
            'relations' => $aDbData
        );
        $this->_getDbGateway()->save($aData);

        return $this->getId();
    }

    /**
     * Method for loading model, if loaded returns true.
     *
     * @param string $sId model id.
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
}
