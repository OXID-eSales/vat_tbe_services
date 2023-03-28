<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

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
     * @return oxDisplayError
     */
    public function getMessage($aInvalidArticles)
    {
        /** @var oxDisplayError $oEx */
        $oEx = oxNew('oxDisplayError');
        /** @var oxLang $oLang */
        $oLang = oxRegistry::getLang();

        /** @var oxArticle $oInvalidArticle */
        $aArticleNames = array();
        foreach ($aInvalidArticles as $oInvalidArticle) {
            $aArticleNames[] = $oInvalidArticle->oxarticles__oxtitle->value;
        }
        $sArticleNames = implode(', ', $aArticleNames);

        $oEx->setMessage(sprintf($oLang->translateString('OEVATTBE_ERROR_MESSAGE_TBE_ARTICLE_VAT_PROBLEMS', $oLang->getTplLanguage()), $sArticleNames));

        return $oEx;
    }
}
