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

use OxidEsales\EVatModule\Core\oeVATTBEModel;
use OxidEsales\EVatModule\Model\DbGateway\oeVATTBECountryVATGroupsDbGateway;

/**
 * VAT Group handling class
 */
class oeVATTBECountryVATGroup extends oeVATTBEModel
{
    private $_oVATGroupArticleCacheInvalidator = null;

    public function __construct(
        protected oeVATTBECountryVATGroupsDbGateway $_oDbGateway
    )
    {
    }

    /**
     * Sets VAT group articles cache invalidator.
     * If this invalidator is not set, cache will not be cleared on save and delete events.
     *
     * @param null $oVATGroupArticleCacheInvalidator Cache invalidator object.
     */
    public function setVATGroupArticleCacheInvalidator($oVATGroupArticleCacheInvalidator)
    {
        $this->_oVATGroupArticleCacheInvalidator = $oVATGroupArticleCacheInvalidator;
    }

    /**
     * Method for model saving (insert and update data).
     *
     * @return int|false
     */
    public function save()
    {
        $blResult = parent::save();
        $this->_invalidateGroupArticlesCache();

        return $blResult;
    }

    /**
     * Delete model data from db.
     *
     * @param string $sId model id
     *
     * @return bool
     */
    public function delete($sId = null)
    {
        $blResult = parent::delete($sId);
        $this->_invalidateGroupArticlesCache();

        return $blResult;
    }

    /**
     * Sets model id.
     *
     * @param string $sId Model id
     */
    public function setId($sId)
    {
        parent::setId($sId);
        $this->_setValue('oevattbe_id', $sId);
    }

    /**
     * Returns country id, for which this group is used.
     *
     * @return string
     */
    public function getCountryId()
    {
        return $this->_getValue('oevattbe_countryid');
    }

    /**
     * Sets country id, for which this group should be used.
     *
     * @param string $sCountryId
     */
    public function setCountryId($sCountryId)
    {
        $this->_setValue('oevattbe_countryid', $sCountryId);
    }

    /**
     * Returns group name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_getValue('oevattbe_name');
    }

    /**
     * Sets group name.
     *
     * @param string $sName
     */
    public function setName($sName)
    {
        $this->_setValue('oevattbe_name', $sName);
    }

    /**
     * Returns group description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_getValue('oevattbe_description');
    }

    /**
     * Sets group description.
     *
     * @param string $sDescription
     */
    public function setDescription($sDescription)
    {
        $this->_setValue('oevattbe_description', $sDescription);
    }

    /**
     * Returns group VAT rate.
     *
     * @return string
     */
    public function getRate()
    {
        return $this->_getValue('oevattbe_rate');
    }

    /**
     * Sets group VAT rate.
     *
     * @param double $dRate
     */
    public function setRate($dRate)
    {
        $this->_setValue('oevattbe_rate', $dRate);
    }

    /**
     * Returns group articles cache invalidator.
     *
     * @return oeVATTBEVATGroupArticleCacheInvalidator
     */
    protected function _getVATGroupArticleCacheInvalidator()
    {
        return $this->_oVATGroupArticleCacheInvalidator;
    }

    /**
     * Clears cache for VAT group articles.
     */
    private function _invalidateGroupArticlesCache()
    {
        $oInvalidator = $this->_getVATGroupArticleCacheInvalidator();
        if ($oInvalidator) {
            $oInvalidator->invalidate($this->getId());
        }
    }
}
