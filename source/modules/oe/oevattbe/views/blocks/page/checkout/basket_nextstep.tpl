[{assign var="oMarkGenerator" value=$oView->getBasketContentMarkGenerator()}]
[{assign var="oCountry" value=$oxcmp_basket->getTBECountry()}]
[{if $oxcmp_basket->hasVATTBEArticles() }]
    [{if !$oxcmp_user}]
        <div class="lineBox clear">
            [{$oMarkGenerator->getMark('tbeService')}] - [{oxmultilang ident="OEVATTBE_VAT_WILL_BE_CALCULATED_BY_USER_COUNTRY"}]
        </div>
    [{elseif $oxcmp_basket->isTBEValid()}]
        [{if $oCountry && $oCountry->appliesTBEVAT() }]
            <div class="lineBox clear">
                [{$oMarkGenerator->getMark('tbeService')}] - [{oxmultilang ident="OEVATTBE_VAT_CALCULATED_BY_USER_COUNTRY" args=$oCountry->oxcountry__oxtitle->value}]
            </div>
        [{/if}]
    [{/if}]
[{/if}]
[{if $oxcmp_user && $oCountry && !$oCountry->isInEU() }]
    <div class="lineBox clear">
        [{oxmultilang ident="OEVATTBE_VAT_EXCLUDED"}]
    </div>
[{/if}]
[{$smarty.block.parent}]