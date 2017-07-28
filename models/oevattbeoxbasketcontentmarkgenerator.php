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

/**
 * VAT TBE oxUser class
 */
class oeVATTBEOxBasketContentMarkGenerator extends oeVATTBEOxBasketContentMarkGenerator_parent
{
    /**
     * Sets basket that is used to get article type(downloadable, intangible etc..).
     *
     * @param oxBasket $oBasket basket
     */
    public function __construct(oxBasket $oBasket)
    {
        $this->_oTBEBasket = $oBasket;
        parent::__construct($oBasket);
    }


    /**
     * Returns explanation mark by given mark identification (skippedDiscount, downloadable, intangible).
     *
     * @param string $sMarkIdentification Mark identification.
     *
     * @return string
     */
    public function getMark($sMarkIdentification)
    {
        if ($this->_oTBEBasket->hasOeTBEVATArticles()) {
            $sCurrentMark = self::DEFAULT_EXPLANATION_MARK;
            $aMarks = $this->_formMarks($sCurrentMark);
            $sMark = $aMarks[$sMarkIdentification];
        } else {
            $sMark = parent::getMark($sMarkIdentification);
        }

        return $sMark;
    }

    /**
     * Forms marks for articles.
     *
     * @param string $sCurrentMark Current mark.
     *
     * @return array
     */
    private function _formMarks($sCurrentMark)
    {
        $oBasket = $this->_oTBEBasket;
        $aMarks = array();

        if ($oBasket->hasOeTBEVATArticles()) {
            $aMarks['tbeService'] = $sCurrentMark;
            $sCurrentMark .= '*';
        }

        if ($oBasket->hasSkipedDiscount()) {
            $aMarks['skippedDiscount'] = $sCurrentMark;
            $sCurrentMark .= '*';
        }
        if ($oBasket->hasArticlesWithDownloadableAgreement()) {
            $aMarks['downloadable'] = $sCurrentMark;
            $sCurrentMark .= '*';
        }
        if ($oBasket->hasArticlesWithIntangibleAgreement()) {
            $aMarks['intangible'] = $sCurrentMark;
        }

        return $aMarks;
    }
}
