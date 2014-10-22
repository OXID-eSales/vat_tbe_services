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

<form action="[{$oViewConf->getSelfLink()}]" method="post" onSubmit="copyLongDesc( 'oxarticles__oxlongdesc' );" style="padding: 0px;margin: 0px;height:0px;">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="oevattbearticleadministration">
    <input type="hidden" name="fnc" value="save">
    <input type="hidden" name="oxid" value="[{$oxid}]">

    <input type="hidden" name="editval[oevattbe_istbeservice]" value="0">
    <input class="edittext" type="checkbox" name="editval[oevattbe_istbeservice]" value='1' [{if $iIsTbeService == 1}]checked[{/if}]>

    <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="OEVATTBE_ARTICLE_UPDATE"}]" onClick="Javascript:document.myedit.fnc.value='save'">
</form>

[{*Required for admin tabs to work*}]
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
[{*/*}]