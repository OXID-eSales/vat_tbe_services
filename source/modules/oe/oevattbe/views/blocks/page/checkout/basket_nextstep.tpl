[{if $oView->oeVATTBEShowVATTBEMarkMessage()}]
    [{if $oViewConf->getActiveTheme() == 'flow'}]
        <div class="well well-sm clear">[{$oView->getOeVATTBEMarkMessage()}]</div>
    [{else}]
        <div class="lineBox clear">[{$oView->getOeVATTBEMarkMessage()}]</div>
    [{/if}]
[{/if}]
[{$smarty.block.parent}]