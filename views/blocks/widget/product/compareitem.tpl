[{$smarty.block.parent}]
[{if $oViewConf->oeVATTBEShowTBEArticlePriceNotice($product) && $oView->isVatIncluded() && $blShowToBasket}]
    <label class="price"><strong>**</strong></label>
[{/if}]