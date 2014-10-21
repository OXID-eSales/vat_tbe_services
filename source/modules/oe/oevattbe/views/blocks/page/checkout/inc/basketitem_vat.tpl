[{* product VAT percent *}]
[{assign var="oCountry" value=$oxcmp_basket->getTBECountry()}]
<td class="vatPercent">
    [{if $oArticle->isTBEService() && $oCountry->appliesTBEVAT()}]
        [{if $oxcmp_user }]
            [{if $oxcmp_basket->isTBEValid()}]
                [{assign var="oArticle" value=$basketitem->getArticle()}]
                [{assign var="oMarkGenerator" value=$oView->getBasketContentMarkGenerator()}]
                [{$basketitem->getVatPercent()}]% [{$oMarkGenerator->getMark('tbeService')}]
            [{else}]
                -
            [{/if}]
        [{else}]
            [{$basketitem->getVatPercent()}]% [{$oMarkGenerator->getMark('tbeService')}]
        [{/if}]
    [{else}]
        [{$basketitem->getVatPercent()}]%
    [{/if}]
</td>