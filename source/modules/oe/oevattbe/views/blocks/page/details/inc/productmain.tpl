[{$smarty.block.parent}]
[{if $oDetailsProduct->isOeVATTBETBEService() && $oView->isVatIncluded()}]
    <label class="price tbePrice"><strong><span>**</span></strong></label>
[{/if}]