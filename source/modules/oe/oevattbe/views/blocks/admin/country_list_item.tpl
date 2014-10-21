[{ if $listitem->blacklist == 1}]
[{assign var="listclass" value=listitem3 }]
[{ else}]
[{assign var="listclass" value=listitem$blWhite }]
[{ /if}]
[{ if $listitem->getId() == $oxid }]
[{assign var="listclass" value=listitem4 }]
[{ /if}]
<td valign="top" class="[{ $listclass}][{ if $listitem->oxcountry__oxactive->value == 1}] active[{/if}]" height="15">
    <div class="listitemfloating">&nbsp;
        <a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxcountry__oxid->value}]');" class="[{ $listclass}]">&nbsp;</a>
    </div>
</td>
<td valign="top" class="[{ $listclass}]" height="15">
    <div class="listitemfloating">
        <a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxcountry__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxcountry__oxtitle->value }]</a>
    </div>
</td>
<td valign="top" class="[{ $listclass}]" height="15">
    <div class="listitemfloating">
        <a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxcountry__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxcountry__oxshortdesc->value }]</a>
    </div>
</td>
<td valign="top" class="[{ $listclass}]" height="15">
    <a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxcountry__oxid->value}]');" class="[{ $listclass}]">
        [{if $listitem->appliesTBEVAT()}]
            [{ oxmultilang ident="OEVATTBE_COUNTRY_IS_TBE" }]
            [{if !$listitem->isOEVATTBEAtLeastOneGroupConfigured()}]
                [{ oxmultilang ident="OEVATTBE_COUNTRY_NO_TBE_GROUPS_CONFIGURED" }]
            [{/if}]
        [{else}]
            [{ oxmultilang ident="OEVATTBE_COUNTRY_IS_NOT_TBE" }]
        [{/if}]
    </a>
</td>
<td valign="top" class="[{ $listclass}]" height="15">
    <div class="listitemfloating">
        <a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxcountry__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxcountry__oxisoalpha3->value }]</a>
    </div>
</td>
<td align="right" class="[{ $listclass}]">
    [{if !$readonly}]
        <a href="Javascript:top.oxid.admin.deleteThis('[{ $listitem->oxcountry__oxid->value }]');" class="delete" id="del.[{$_cnt}]" title="" [{include file="help.tpl" helpid=item_delete}]></a>
    [{/if}]
</td>
