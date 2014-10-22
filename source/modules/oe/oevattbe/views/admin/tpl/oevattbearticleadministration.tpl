[{*Required for admin tabs to work*}]
[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="oxidCopy" value="[{$oxid}]">
    <input type="hidden" name="cl" value="article_main">
    <input type="hidden" name="language" value="[{$actlang}]">
</form>
[{*/*}]

<style>
    .vattbeAdministration {
        border: solid 1px #A9A9A9;
        padding: 5px;
        width: 600px;
    }
    .vattbeAdministrationList {
        width: 100%;
        margin-top: 5px;
    }
    .vattbeAdministrationList th {
        text-align: left;
    }
</style>

<form action="[{$oViewConf->getSelfLink()}]" method="post" style="padding: 0px;margin: 0px;height:0px;">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="oevattbearticleadministration">
    <input type="hidden" name="fnc" value="save">
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <table class="vattbeAdministration">
        <tr>
            <td class="edittext" width="120">
                [{oxmultilang ident="OEVATTBE_ARTICLE_UPDATE_LABEL"}]
            </td>
            <td class="edittext">
                <input type="hidden" name="editval[oevattbe_istbeservice]" value="0">
                <input class="edittext" type="checkbox" name="editval[oevattbe_istbeservice]" value='1' [{if $iIsTbeService == 1}]checked[{/if}]>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table class="vattbeAdministrationList">
                    <tr>
                        <th>
                            [{oxmultilang ident="OEVATTBE_ARTICLE_COUNTRY"}]
                        </th>
                        <th>
                            [{oxmultilang ident="OEVATTBE_ARTICLE_VAT_GROUP"}]
                        </th>
                    </tr>
                    <tr>
                        <td>Lithuania</td>
                        <td>
                            <select>
                                <option>Standard rate - 19%</option>
                                <option>Reduce rate 1 - 6%</option>
                                <option>Parking rate - 12%</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Germany</td>
                        <td>
                            <select>
                                <option>Standard rate - 19%</option>
                                <option>Reduce rate 1 - 6%</option>
                                <option>Parking rate - 12%</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="OEVATTBE_ARTICLE_UPDATE_BUTTON"}]" onClick="Javascript:document.myedit.fnc.value='save'">
            </td>
        </tr>
    </table>
</form>

[{*Required for admin tabs to work*}]
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
[{*/*}]