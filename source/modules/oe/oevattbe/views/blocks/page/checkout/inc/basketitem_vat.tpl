[{* product VAT percent *}]
<td class="vatPercent">
    [{if $oView->isOeVATTBETBEArticleValid($basketitem)}]
        [{$basketitem->getVatPercent()}]%
        [{if $oView->oeVATTBEShowVATTBEMark($basketitem)}]
            **
        [{/if}]
    [{else}]
        -
    [{/if}]
</td>