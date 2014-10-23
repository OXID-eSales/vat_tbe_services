[{if $oView->getTBEMarkMessage()}]
    <div>
        [{$oView->getTBEMarkMessage()}]
    </div>
[{/if}]
[{if $oxcmp_user && $oCountry && !$oCountry->isInEU() }]
    <div class="lineBox clear">
        [{oxmultilang ident="OEVATTBE_VAT_EXCLUDED"}]
    </div>
    [{/if}]
[{$smarty.block.parent}]