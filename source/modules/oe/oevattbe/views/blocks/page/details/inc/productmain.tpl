[{$smarty.block.parent}]
[{if $oViewConf->oeVATTBEShowTBEArticlePriceNotice($oDetailsProduct) && $oView->isVatIncluded()}]
    <label class="price tbePrice"><strong><span>**</span></strong></label>
[{/if}]