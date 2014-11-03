[{* product VAT percent *}]
<td class="vatPercent">
    [{if $oView->isOeVATTBETBEArticleValid($basketitem)}]
        [{$basketitem->getVatPercent()}]%
        [{if $oView->oeVATTBEShowVATTBEMark($basketitem)}]
            [{assign var=markGenerator value=$oView->getBasketContentMarkGenerator()}]
            [{$markGenerator->getMark('tbeService')}]
        [{/if}]
    [{else}]
        -
    [{/if}]
</td>