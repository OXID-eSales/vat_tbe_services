[{assign var="oCountry" value=$oxcmp_basket->getTBECountry()}]
[{assign var="oMarkGenerator" value=$oView->getBasketContentMarkGenerator()}]
[{if $oxcmp_basket->hasVATTBEArticles() && $oxcmp_basket->isTBEValid() && $oCountry->appliesTBEVAT()}]
    <div>
        [{$oMarkGenerator->getMark('tbeService')}] - [{oxmultilang ident="OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY" args=$oCountry->oxcountry__oxtitle->value}]
    </div>
[{/if}]
[{if $oxcmp_user && $oCountry && !$oCountry->isInEU() }]
    <div class="lineBox clear">
        [{oxmultilang ident="OEVATTBE_VAT_EXCLUDED"}]
    </div>
    [{/if}]
[{$smarty.block.parent}]