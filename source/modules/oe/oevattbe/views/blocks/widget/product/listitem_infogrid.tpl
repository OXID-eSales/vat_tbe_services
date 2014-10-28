[{$smarty.block.parent}]
[{if $product->isOeVATTBETBEService() && $oView->isVatIncluded()}]
[{if !($product->hasMdVariants() || ($oViewConf->showSelectListsInList() && $product->getSelections(1)) || $product->getVariants())}]
    <span class="price tbePrice">**</span>
[{/if}]
[{/if}]