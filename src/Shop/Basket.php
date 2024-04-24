<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Shop;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Country as EShopCountry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EVatModule\Model\OrderArticleChecker;
use OxidEsales\EVatModule\Model\User;

/**
 * VAT TBE oxBasket class
 */
class Basket extends Basket_parent
{
    /**
     * TBE country id
     *
     * @var string
     */
    private $_sTBECountryId = null;

    /** @var bool store info about tbe country changes */
    private $_isTBECountryChanged = false;

    /** @var array Basket discounts information */
    protected $aDiscounts = [];

    /**
     * Return tbe country id
     *
     * @return string
     */
    public function getOeVATTBETbeCountryId()
    {
        return $this->_sTBECountryId;
    }

    /**
     * Returns if basket has tbe articles in it.
     *
     * @return bool
     */
    public function hasOeTBEVATArticles()
    {
        $blHasTBEArticles = false;
        $oBasketArticles = $this->getBasketArticles();
        foreach ($oBasketArticles as $oArticle) {
            /** @var Article $oArticle */
            if ($oArticle->isOeVATTBETBEService()) {
                $blHasTBEArticles = true;
                break;
            }
        }

        return $blHasTBEArticles;
    }

    /**
     * Set tbe country id
     *
     * @param string $sTBECountryId tbe country id
     */
    public function setOeVATTBECountryId($sTBECountryId)
    {
        if ($this->_sTBECountryId !== $sTBECountryId) {
            $this->_sTBECountryId = $sTBECountryId;
            $this->onOeVATTBECountryChange();
        }
    }

    /**
     * Returns TBE country
     *
     * @return EShopCountry|Country
     */
    public function getOeVATTBECountry()
    {
        if (is_null($this->getOeVATTBETbeCountryId())) {
            return null;
        }

        $oCountry = oxNew(EShopCountry::class);
        $oCountry->load($this->getOeVATTBETbeCountryId());

        return $oCountry;
    }

    /**
     * Setter to set country was changed or not
     *
     * @param bool $blChanged changed ot not
     *
     * @return bool
     */
    public function setOeVATTBECountryChanged($blChanged = true)
    {
        return $this->_isTBECountryChanged = $blChanged;
    }

    /**
     * Return true on show error only for one time
     *
     * @return bool
     */
    public function showOeVATTBECountryChangedError()
    {
        $blChanged = $this->_isTBECountryChanged;
        $this->setOeVATTBECountryChanged(false);

        return $blChanged;
    }

    /**
     * Return if basket is valid according TBE rules
     *
     * @return bool
     */
    public function isOeVATTBEValid()
    {
        return $this->getOeVATTBEOrderArticleChecker()->isValid();
    }

    /**
     * Return if basket is valid according TBE rules
     *
     * @return bool
     */
    public function getOeVATTBEInValidArticles()
    {
        return $this->getOeVATTBEOrderArticleChecker()->getInvalidArticles();
    }

    /**
     * Return tbe article checker
     *
     * @return OrderArticleChecker
     */
    protected function getOeVATTBEOrderArticleChecker()
    {
        return ContainerFacade::get(OrderArticleChecker::class);
    }

    /**
     * Executes necessary actions on basket country change.
     */
    private function onOeVATTBECountryChange()
    {
        $oUserCountry = ContainerFacade::get(User::class);
        $oCountry = $this->getOeVATTBECountry();

        $blUserFromDomesticCountry = $oUserCountry->isUserFromDomesticCountry();
        $blCountryAppliesTBEVAT = $oCountry && $oCountry->appliesOeTBEVATTbeVat();
        if (!$blUserFromDomesticCountry && $blCountryAppliesTBEVAT) {
            $this->setOeVATTBECountryChanged();
        }

        $this->calculateBasket(true);
    }
}
