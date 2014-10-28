[{$smarty.block.parent}]
[{if $product->isOeVATTBETBEService() && $oView->isVatIncluded() && $blShowToBasket}]
    <label class="price"><strong>**</strong></label>
[{/if}]