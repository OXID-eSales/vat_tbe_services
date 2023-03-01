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

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\DisplayError;
use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\Registry;

/**
 * Forms error message if there are articles with invalid TBE VAT.
 */
class oeVATTBEIncorrectVATArticlesMessageFormatter
{
    /**
     * Forms error message for articles with TBE VAT problems.
     *
     * @param array $aInvalidArticles Takes titles to form error message.
     *
     * @return DisplayError
     */
    public function getMessage($aInvalidArticles)
    {
        /** @var DisplayError $oEx */
        $oEx = oxNew(DisplayError::class);

        /** @var Language $oLang */
        $oLang = Registry::getLang();

        /** @var Article $oInvalidArticle */
        $aArticleNames = array();
        foreach ($aInvalidArticles as $oInvalidArticle) {
            $aArticleNames[] = $oInvalidArticle->oxarticles__oxtitle->value;
        }
        $sArticleNames = implode(', ', $aArticleNames);

        $oEx->setMessage(sprintf($oLang->translateString('OEVATTBE_ERROR_MESSAGE_TBE_ARTICLE_VAT_PROBLEMS', $oLang->getTplLanguage()), $sArticleNames));

        return $oEx;
    }
}
