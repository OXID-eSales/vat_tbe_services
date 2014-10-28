[{$smarty.block.parent}]
[{if $oDetailsProduct->oeVATTBEisTBEService() && $oView->isVatIncluded()}]
    <label class="price tbePrice"><strong><span>**</span></strong></label>
[{/if}]