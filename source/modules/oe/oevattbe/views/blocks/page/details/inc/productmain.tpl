[{$smarty.block.parent}]
[{if $oViewConf->oeVATTBEShowTBEArticlePriceNotice($oDetailsProduct) && $oView->isVatIncluded()}]
    [{if $oViewConf->getActiveTheme() == 'flow'}]
        <label class="lead text-nowrap tbePrice price-markup"><span[{if $tprice && $tprice->getBruttoPrice() > $price->getBruttoPrice()}] class="text-danger"[{/if}]>**</span></label>
    [{else}]
        <label class="price tbePrice"><strong><span>**</span></strong></label>
    [{/if}]
[{/if}]