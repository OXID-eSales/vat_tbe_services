[{if $oView->oeVATTBEShowVATTBEMarkMessage()}]
    <div class="lineBox clear">
        [{$oView->getOeVATTBEMarkMessage()}]
    </div>
[{/if}]
[{$smarty.block.parent}]