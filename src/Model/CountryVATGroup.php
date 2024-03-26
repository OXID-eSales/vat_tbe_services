<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model;

use OxidEsales\EVatModule\Core\Model;
use OxidEsales\EVatModule\Model\DbGateway\CountryVATGroupsDbGateway;

/**
 * VAT Group handling class
 */
class CountryVATGroup extends Model
{
    public function __construct(
        protected CountryVATGroupsDbGateway $dbGateway,
        protected ?GroupArticleCacheInvalidator $groupArticleCacheInvalidator = null
    )
    {
    }

    /**
     * Sets VAT group articles cache invalidator.
     * If this invalidator is not set, cache will not be cleared on save and delete events.
     *
     * @param null GroupArticleCacheInvalidator Cache invalidator object.
     */
    public function setVATGroupArticleCacheInvalidator($groupArticleCacheInvalidator)
    {
        $this->groupArticleCacheInvalidator = $groupArticleCacheInvalidator;
    }

    /**
     * Method for model saving (insert and update data).
     *
     * @return int|false
     */
    public function save()
    {
        $blResult = parent::save();
        $this->invalidateGroupArticlesCache();

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
        $this->invalidateGroupArticlesCache();

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
        $this->setValue('OEVATTBE_ID', $sId);
    }

    /**
     * Returns country id, for which this group is used.
     *
     * @return string
     */
    public function getCountryId()
    {
        return $this->getValue('OEVATTBE_COUNTRYID');
    }

    /**
     * Sets country id, for which this group should be used.
     *
     * @param string $sCountryId
     */
    public function setCountryId($sCountryId)
    {
        $this->setValue('OEVATTBE_COUNTRYID', $sCountryId);
    }

    /**
     * Returns group name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getValue('OEVATTBE_NAME');
    }

    /**
     * Sets group name.
     *
     * @param string $sName
     */
    public function setName($sName)
    {
        $this->setValue('OEVATTBE_NAME', $sName);
    }

    /**
     * Returns group description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getValue('OEVATTBE_DESCRIPTION');
    }

    /**
     * Sets group description.
     *
     * @param string $sDescription
     */
    public function setDescription($sDescription)
    {
        $this->setValue('OEVATTBE_DESCRIPTION', $sDescription);
    }

    /**
     * Returns group VAT rate.
     *
     * @return string
     */
    public function getRate()
    {
        return $this->getValue('OEVATTBE_RATE');
    }

    /**
     * Sets group VAT rate.
     *
     * @param double $dRate
     */
    public function setRate($dRate)
    {
        $this->setValue('OEVATTBE_RATE', $dRate);
    }

    /**
     * Returns group articles cache invalidator.
     *
     * @return GroupArticleCacheInvalidator
     */
    protected function getVATGroupArticleCacheInvalidator()
    {
        return $this->groupArticleCacheInvalidator;
    }

    /**
     * Clears cache for VAT group articles.
     */
    private function invalidateGroupArticlesCache()
    {
        $oInvalidator = $this->getVATGroupArticleCacheInvalidator();
        if ($oInvalidator) {
            $oInvalidator->invalidate($this->getId());
        }
    }
}
