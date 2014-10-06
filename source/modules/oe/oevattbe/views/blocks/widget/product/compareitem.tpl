[{$smarty.block.parent}]
[{if $product->isTbeService() && $oView->isVatIncluded() && $blShowToBasket}]
    <label class="price"><strong>**</strong></label>
[{/if}]