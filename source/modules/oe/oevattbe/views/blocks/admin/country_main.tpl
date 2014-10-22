[{$smarty.block.parent}]
<tr>
    <td class="edittext" width="120">
        [{oxmultilang ident="OEVATTBE_TBE_COUNTRY" }]
    </td>
    <td class="edittext">
        <input type="hidden" name="editval[oxcountry__oevattbe_appliestbevat]" value="0">
        <input class="edittext" type="checkbox" name="editval[oxcountry__oevattbe_appliestbevat]" value='1' [{if $edit->oxcountry__oevattbe_appliestbevat->value == 1}]checked[{/if}] [{ $readonly }]>
        [{oxinputhelp ident="HELP_OEVATTBE_TBE_COUNTRY" }]
    </td>
</tr>