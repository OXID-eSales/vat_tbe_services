[{if $oView->oeVATTBEShowVATTBEMarkMessage()}]
    [{if $oViewConf->isActiveThemeBasedOnFlow()}]
        <div class="well well-sm clear">[{$oView->getOeVATTBEMarkMessage()}]</div>
    [{else}]
        <div>[{$oView->getOeVATTBEMarkMessage()}]</div>
    [{/if}]
[{/if}]
[{$smarty.block.parent}]