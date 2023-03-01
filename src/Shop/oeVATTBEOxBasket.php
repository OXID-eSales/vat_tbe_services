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

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\EVatModule\Model\oeVATTBEOrderArticleChecker;
use OxidEsales\EVatModule\Model\oeVATTBETBEUser;

/**
 * VAT TBE oxBasket class
 */
class oeVATTBEOxBasket extends oeVatTbeOxBasket_parent
{
    /**
     * TBE country id
     *
     * @var string
     */
    private $_sTBECountryId = null;


    /** @var bool store info about tbe country changes */
    private $_isTBECountryChanged = false;

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
            $this->_onOeVATTBECountryChange();
        }
    }

    /**
     * Returns TBE country
     *
     * @return Country|oeVATTBEOxCountry
     */
    public function getOeVATTBECountry()
    {
        if (!is_null($this->getOeVATTBETbeCountryId())) {
            $oCountry = oxNew(Country::class);
            $oCountry->load($this->getOeVATTBETbeCountryId());
        }

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
        return $this->_getOeVATTBEOrderArticleChecker()->isValid();
    }

    /**
     * Return if basket is valid according TBE rules
     *
     * @return bool
     */
    public function getOeVATTBEInValidArticles()
    {
        return $this->_getOeVATTBEOrderArticleChecker()->getInvalidArticles();
    }

    /**
     * Return tbe article checker
     *
     * @return oeVATTBEOrderArticleChecker
     */
    protected function _getOeVATTBEOrderArticleChecker()
    {
        $oTBEUser = oeVATTBETBEUser::createInstance();
        return oxNew(oeVATTBEOrderArticleChecker::class, $this->getBasketArticles(), $oTBEUser);
    }

    /**
     * Executes necessary actions on basket country change.
     */
    private function _onOeVATTBECountryChange()
    {
        $oUserCountry = oeVATTBETBEUser::createInstance();
        $oCountry = $this->getOeVATTBECountry();

        $blUserFromDomesticCountry = $oUserCountry->isUserFromDomesticCountry();
        $blCountryAppliesTBEVAT = $oCountry && $oCountry->appliesOeTBEVATTbeVat();
        if (!$blUserFromDomesticCountry && $blCountryAppliesTBEVAT) {
            $this->setOeVATTBECountryChanged();
        }

        $this->calculateBasket(true);
    }
}
