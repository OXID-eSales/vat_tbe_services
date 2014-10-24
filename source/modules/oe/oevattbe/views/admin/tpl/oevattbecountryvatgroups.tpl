[{*Required for admin tabs to work*}]
[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<script type="text/javascript">
    <!--
    window.onload = function ()
    {
        [{if $updatelist == 1}]
        top.oxid.admin.updateList('[{$oxid}]');
        [{/if}]
        var oField = top.oxid.admin.getLockTarget();
        oField.onchange = oField.onkeyup = oField.onmouseout = top.oxid.admin.unlockSave;
    }
    //-->
</script>
<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="oxidCopy" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="country_main">
    <input type="hidden" name="language" value="[{ $actlang }]">
</form>
[{*/Required for admin tabs to work*}]

<table cellspacing="0" cellpadding="0" border="0" width="50%">
    <tr>
        <td valign="top" class="edittext" style="width:50%">
            <form name="countryVATGroupList" id="countryVATGroupList" action="[{$oViewConf->getSelfLink()}]" method="post">
                [{$oViewConf->getHiddenSid()}]
                <input type="hidden" name="oxid" value="[{$oxid}]">
                <input type="hidden" name="oxidCopy" value="[{$oxid}]">
                <input type="hidden" name="cl" value="oeVATTBECountryVatGroups">
                <input type="hidden" name="fnc" value="changeCountryVATGroups">
                <input type="hidden" name="language" value="[{$actlang}]">
                <table cellspacing="0" cellpadding="1" border="0" width="98%">
                    [{assign var=oddclass value="2"}]
                    [{assign var=aVatGroups value=$oView->getVatGroups()}]
                    <colgroup>
                        [{block name="admin_country_list_colgroup"}]
                            <col width="1%">
                            <col width="1%">
                            <col width="96%">
                            <col width="1%" >
                        [{/block}]
                    </colgroup>
                    <tr>
                        <td class="listheader first">[{oxmultilang ident="OEVATTBE_COUNTRY_VAT_GROUP_NAME"}]</td>
                        <td class="listheader">[{oxmultilang ident="OEVATTBE_COUNTRY_VAT_GROUP_VALUE"}]</td>
                        <td class="listheader">[{oxmultilang ident="OEVATTBE_COUNTRY_VAT_GROUP_DESCRIPTION"}]</td>
                        <td class="listheader"></td>
                    </tr>
                    [{foreach from=$aVatGroups item=oVatGroup}]
                        <tr>
                            [{if $oddclass == 2}]
                                [{assign var=oddclass value=""}]
                            [{else}]
                                [{assign var=oddclass value="2"}]
                            [{/if}]
                            <td class="listitem[{$oddclass}]" nowrap="nowrap" valign="top">
                                <input type="text" size="25" name="updateval[[{$oVatGroup->getId()}]][oevattbe_name]" value="[{$oVatGroup->getName()}]" />
                            </td>
                            <td class="listitem[{$oddclass}]" nowrap="nowrap" valign="top">
                                <input type="text" size="5" name="updateval[[{$oVatGroup->getId()}]][oevattbe_rate]" value="[{$oVatGroup->getRate()}]" />
                            </td>
                            <td class="listitem[{$oddclass}]" nowrap="nowrap" valign="top">
                                <textarea class="editinput" cols="28" rows="1" wrap="VIRTUAL" name="updateval[[{$oVatGroup->getId()}]][oevattbe_description]">[{$oVatGroup->getDescription()}]</textarea>
                            </td>
                            <td class=listitem[{$oddclass}]>
                                <input type="hidden" name="updateval[[{$oVatGroup->getId()}]][oevattbe_id]" value="[{$oVatGroup->getId()}]">
                                <a [{$readonly}] href="[{ $oViewConf->getSelfLink() }]&cl=oeVATTBECountryVatGroups&countryVATGroupId=[{$oVatGroup->getId()}]&fnc=deleteCountryVatGroup&oxid=[{$oxid}]" onClick='return confirm("[{ oxmultilang ident="GENERAL_YOUWANTTODELETE" }]")' [{if $readonly }]onclick="JavaScript:return false;"[{/if}] class="delete"></a>
                            </td>
                        </tr>
                    [{/foreach}]
                    [{if count($aVatGroups) > 0}]
                        <tr>
                            <td colspan="4"><br>
                                <input type="submit" class="edittext" name="saveAll" value="[{ oxmultilang ident="OEVATTBE_COUNTRY_VAT_GROUP_SAVE" }]"><br><br>
                            </td>
                        </tr>
                    [{/if}]
                </table>
            </form>
        </td>
    </tr>
    [{if count($aVatGroups) > 0}]
    <tr>
        <td>
            <hr />
        </td>
    </tr>
    [{/if}]
    <tr>
        <td valign="top">
            <form name="addCountryVATGroup" id="addCountryVATGroup" action="[{$oViewConf->getSelfLink()}]" method="post">
                [{$oViewConf->getHiddenSid()}]
                <input type="hidden" name="oxid" value="[{$oxid}]">
                <input type="hidden" name="oxidCopy" value="[{$oxid}]">
                <input type="hidden" name="cl" value="oeVATTBECountryVatGroups">
                <input type="hidden" name="fnc" value="addCountryVATGroup">
                <input type="hidden" name="language" value="[{$actlang}]">
                <input type="hidden" name="editval[oxcountry__oxid]" value="[{$oxid}]">
                <fieldset style="padding-left: 5px;" title="new group form">
                    <legend>[{oxmultilang ident="OEVATTBE_CREATE_NEW_COUNTRY_VAT_GROUP_LEGEND"}]</legend>
                    <table cellspacing="0" cellpadding="0" border="0" width="50%">
                        <tr>
                            <td class="edittext" valign="top">
                                <table>
                                    <tr>
                                        <td class="edittext">
                                            [{oxmultilang ident="OEVATTBE_COUNTRY_VAT_GROUP_NAME"}]
                                        </td>
                                        <td class="edittext">
                                            <input class="edittext" size="25" type="text" name="editval[oevattbe_name]">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="edittext">
                                            [{oxmultilang ident="OEVATTBE_COUNTRY_VAT_GROUP_VALUE"}]
                                        </td>
                                        <td class="edittext" nowrap="nowrap">
                                            <input class="edittext" type="text" name="editval[oevattbe_rate]" size="5">
                                            [{oxinputhelp ident="OEVATTBE_HELP_COUNTRY_VAT_GROUP_VALUE"}]
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="edittext" valign="top">
                                            [{oxmultilang ident="OEVATTBE_COUNTRY_VAT_GROUP_DESCRIPTION"}]
                                        </td>
                                        <td class="edittext" nowrap="nowrap">
                                            <textarea class="editinput" cols="28" rows="1" wrap="VIRTUAL" name="editval[oevattbe_description]"></textarea>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td><br>
                                <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="OEVATTBE_COUNTRY_VAT_GROUP_SAVE"}]"><br><br>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </td>
    </tr>
</table>

[{*Required for admin tabs to work*}]
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
[{*/Required for admin tabs to work*}]