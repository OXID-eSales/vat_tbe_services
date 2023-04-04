<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Model;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\DisplayError;
use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\Registry;

/**
 * Forms error message if there are articles with invalid TBE VAT.
 */
class IncorrectVATArticlesMessageFormatter
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

        /** @var Article $invalidArticle */
        $aArticleNames = array();
        foreach ($aInvalidArticles as $invalidArticle) {
            $aArticleNames[] = $invalidArticle->getFieldData('oxtitle');
        }
        $sArticleNames = implode(', ', $aArticleNames);

        $oEx->setMessage(sprintf($oLang->translateString('OEVATTBE_ERROR_MESSAGE_TBE_ARTICLE_VAT_PROBLEMS', $oLang->getTplLanguage()), $sArticleNames));

        return $oEx;
    }
}
