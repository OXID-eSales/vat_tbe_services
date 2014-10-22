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

<form action="[{$oViewConf->getSelfLink()}]" method="post" style="padding: 0px;margin: 0px;height:0px;">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="oevattbearticleadministration">
    <input type="hidden" name="fnc" value="save">
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <table>
        <tbody>
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
                    <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="OEVATTBE_ARTICLE_UPDATE_BUTTON"}]" onClick="Javascript:document.myedit.fnc.value='save'">
                </td>
            </tr>
        </tbody>
    </table>
</form>

[{*Required for admin tabs to work*}]
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
[{*/*}]