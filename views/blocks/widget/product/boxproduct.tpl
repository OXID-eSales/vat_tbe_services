[{$smarty.block.parent}]
[{if $oViewConf->oeVATTBEShowTBEArticlePriceNotice($_oBoxProduct) && $oView->isVatIncluded()}]
[{if !($_oBoxProduct->getVariantsCount() || $_oBoxProduct->hasMdVariants() || ($oViewConf->showSelectListsInList() && $_oBoxProduct->getSelections(1)))}]
    <strong><span>**</span></strong>
[{/if}]
[{/if}]