[{$smarty.block.parent}]
[{if $oDetailsProduct->isTbeService() && $oView->isVatIncluded()}]
    <label class="price tbePrice"><strong><span>**</span></strong></label>
[{/if}]