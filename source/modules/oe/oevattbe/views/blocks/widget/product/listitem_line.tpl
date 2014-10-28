[{$smarty.block.parent}]
[{if $product->isOeVATTBETBEService() && $oView->isVatIncluded()}]
[{if !($product->hasMdVariants() || ($oViewConf->showSelectListsInList() && $product->getSelections(1)) || $product->getVariants())}]
    <label class="price tbePrice">**</label>
[{/if}]
[{/if}]