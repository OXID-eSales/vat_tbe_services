<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
