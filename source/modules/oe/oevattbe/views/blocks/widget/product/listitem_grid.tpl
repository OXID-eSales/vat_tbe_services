[{$smarty.block.parent}]
[{if $oViewConf->oeVATTBEShowTBEArticlePriceNotice($product) && $oView->isVatIncluded()}]
    [{if !($product->hasMdVariants() || ($oViewConf->showSelectListsInList() && $product->getSelections(1)) || $product->getVariants())}]
        [{if $oViewConf->getActiveTheme() == 'flow'}]
            <span class="lead text-nowrap">**</span>
        [{else}]
            <strong><span>**</span></strong>
        [{/if}]
    [{/if}]
[{/if}]