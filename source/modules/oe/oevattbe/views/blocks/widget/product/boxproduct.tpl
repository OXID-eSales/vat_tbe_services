[{$smarty.block.parent}]
[{if $_oBoxProduct->isTbeService() && $oView->isVatIncluded()}]
[{if !( $_oBoxProduct->getVariantsCount() || $_oBoxProduct->hasMdVariants() || ($oViewConf->showSelectListsInList()&&$_oBoxProduct->getSelections(1)) )}]
    <strong><span>**</span></strong>
[{/if}]
[{/if}]