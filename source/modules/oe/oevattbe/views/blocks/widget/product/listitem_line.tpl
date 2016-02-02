[{$smarty.block.parent}]
[{if $oViewConf->oeVATTBEShowTBEArticlePriceNotice($product) && $oView->isVatIncluded()}]
    [{if !($product->hasMdVariants() || ($oViewConf->showSelectListsInList() && $product->getSelections(1)) || $product->getVariants())}]
        <label class="price lead text-nowrap tbePrice">**</label>
    [{/if}]
[{/if}]