[{$smarty.block.parent}]
[{if $_product->isOeVATTBETBEService() && $oView->isVatIncluded()}]
[{if !( $_product->getVariantsCount() || $_product->hasMdVariants() || ($oViewConf->showSelectListsInList()&&$_product->getSelections(1)) )}]
    <span class="price priceValue tbePrice"><div><span class="priceValue">**</span></div></span>
[{/if}]
[{/if}]