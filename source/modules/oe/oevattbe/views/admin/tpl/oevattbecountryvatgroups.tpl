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

<form name="addCountryVATGroup" id="addCountryVATGroup" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="oxidCopy" value="[{$oxid}]">
    <input type="hidden" name="cl" value="oeVATTBECountryVatGroups">
    <input type="hidden" name="fnc" value="addCountryVATGroup">
    <input type="hidden" name="language" value="[{$actlang}]">
    <input type="hidden" name="editval[oxcountry__oxid]" value="[{$oxid}]">
    <table cellspacing="0" cellpadding="0" border="0" width="98%">
        <tr>
            <td class="edittext" valign="top">
                <table>
                    <tr>
                        <td class="edittext">
                            [{oxmultilang ident="OEVATTBE_COUNTRY_VAT_GROUP_NAME"}]
                        </td>
                        <td class="edittext">
                            <input class="edittext" type="text" name="editval[oevattbe_name]">
                        </td>
                    </tr>
                    <tr>
                        <td class="edittext">
                            [{oxmultilang ident="OEVATTBE_COUNTRY_VAT_GROUP_VALUE"}]
                        </td>
                        <td class="edittext" nowrap="nowrap">
                            <input class="edittext" type="text" name="editval[oevattbe_rate]">
                            [{oxinputhelp ident="OEVATTBE_HELP_COUNTRY_VAT_GROUP_VALUE"}]
                        </td>
                    </tr>
                    <tr>
                        <td class="edittext">
                            [{oxmultilang ident="OEVATTBE_COUNTRY_VAT_GROUP_DESCRIPTION"}]
                        </td>
                        <td class="edittext" nowrap="nowrap">
                            <input class="edittext" type="text" name="editval[oevattbe_description]">
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
</form>

[{*Required for admin tabs to work*}]
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
[{*/Required for admin tabs to work*}]