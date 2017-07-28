[{if $oView->oeVATTBEShowVATTBEMarkMessage()}]
    [{if $oViewConf->getActiveTheme() == 'flow'}]
        <div class="well well-sm clear">[{$oView->getOeVATTBEMarkMessage()}]</div>
    [{else}]
        <div>[{$oView->getOeVATTBEMarkMessage()}]</div>
    [{/if}]
[{/if}]
[{$smarty.block.parent}]