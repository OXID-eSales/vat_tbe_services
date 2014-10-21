[{assign var="oMarkGenerator" value=$oView->getBasketContentMarkGenerator()}]
[{if $oxcmp_basket->hasVATTBEArticles() }]
    [{if !$oxcmp_user}]
        <div class="lineBox clear">
            [{$oMarkGenerator->getMark('tbeService')}] - [{oxmultilang ident="OEVATTBE_VAT_WILL_BE_CALCULATED_BY_USER_COUNTRY"}]
        </div>
    [{elseif $oxcmp_basket->isTBEValid()}]
        [{assign var="oCountry" value=$oxcmp_basket->getTBECountry()}]
        [{if $oCountry && $oCountry->appliesTBEVAT() }]
            <div class="lineBox clear">
                [{$oMarkGenerator->getMark('tbeService')}] - [{oxmultilang ident="OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY" args=$oCountry->oxcountry__oxtitle->value}]
            </div>
        [{/if}]
    [{/if}]
[{/if}]
[{$smarty.block.parent}]