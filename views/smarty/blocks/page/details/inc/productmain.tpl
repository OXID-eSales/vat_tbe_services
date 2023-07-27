[{$smarty.block.parent}]
[{if $oViewConf->oeVATTBEShowTBEArticlePriceNotice($oDetailsProduct) && $oView->isVatIncluded()}]
    [{if $oViewConf->isActiveThemeBasedOnFlow()}]
        <label class="lead text-nowrap tbePrice price-markup"><span[{if $tprice && $tprice->getBruttoPrice() > $price->getBruttoPrice()}] class="text-danger"[{/if}]>**</span></label>
    [{else}]
        <label class="price tbePrice"><strong><span>**</span></strong></label>
    [{/if}]
[{/if}]