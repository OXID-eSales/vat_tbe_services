[{* product VAT percent *}]
<td class="vatPercent">
    [{assign var="oArticle" value=$basketitem->getArticle()}]
    [{assign var="oMarkGenerator" value=$oView->getBasketContentMarkGenerator()}]
    [{$basketitem->getVatPercent()}]% [{if $oArticle->isTBEService()}][{$oMarkGenerator->getMark('tbeService')}][{/if}]
</td>