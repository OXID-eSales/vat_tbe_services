[{$smarty.block.parent}]
[{if $product->oeVATTBEisTBEService() && $oView->isVatIncluded() && $blShowToBasket}]
    <label class="price"><strong>**</strong></label>
[{/if}]