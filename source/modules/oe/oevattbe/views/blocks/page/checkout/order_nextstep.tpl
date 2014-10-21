[{assign var="oCountry" value=$oxcmp_basket->getTBECountry()}]
[{if $oxcmp_basket->hasVATTBEArticles() && $oCountry && $oxcmp_basket->isTBEValid() && $oCountry->appliesTBEVAT()}]
    [{assign var="oMarkGenerator" value=$oView->getBasketContentMarkGenerator()}]
    [{assign var="oCountry" value=$oxcmp_basket->getTBECountry()}]
    <div>
        [{$oMarkGenerator->getMark('tbeService')}] - [{oxmultilang ident="OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY" args=$oCountry->oxcountry__oxtitle->value}]
    </div>
    [{/if}]
[{$smarty.block.parent}]