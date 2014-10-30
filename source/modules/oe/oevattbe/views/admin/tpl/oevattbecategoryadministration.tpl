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
    }
    .vattbeAdministrationList th {
        text-align: left;
    }
    .vattbeAdministrationList select {
        min-width: 180px;
    }
</style>

<form action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="oevattbecategoryadministration">
    <input type="hidden" name="fnc" value="save">
    <input type="hidden" name="oxid" value="[{$oxid}]">

    <p>
        <label for="isOeVATTBETBEService">[{oxmultilang ident="OEVATTBE_CATEGORY_SUBMIT_LABEL"}]</label>
        <input type="hidden" name="editval[oevattbe_istbe]" value="0">
        <input id="isOeVATTBETBEService" class="edittext" type="checkbox" name="editval[oevattbe_istbe]" value="1" [{if $iIsTbeService == 1}]checked[{/if}]>
    </p>
    <table class="vattbeAdministration">
        <tr>
            <td colspan="2">
                <table class="vattbeAdministrationList">
                    <tr>
                        <td colspan="2">
                            [{oxmultilang ident="OEVATTBE_VAT_RATES"}]
                        </td>
                    </tr>
                    <tr>
                        <th>
                            [{oxmultilang ident="OEVATTBE_CATEGORY_COUNTRY"}]
                        </th>
                        <th>
                            [{oxmultilang ident="OEVATTBE_CATEGORY_VAT_GROUP"}]
                        </th>
                    </tr>
                    [{foreach from=$aCountriesAndVATGroups key=sCountryId item=aVATInformation}]
                    <tr>
                        <td>[{$aVATInformation.countryTitle}]</td>
                        <td>
                            <select name="VATGroupsByCountry[[{$sCountryId}]]">
                                <option value="">[{oxmultilang ident="OEVATTBE_CHOOSE_VAT_RATE"}]</option>
                                [{foreach from=$aVATInformation.countryGroups item=oVATTBECountryVATGroup}]
                                    <option value="[{$oVATTBECountryVATGroup->getId()}]"
                                        [{if $oView->isSelected($sCountryId, $oVATTBECountryVATGroup->getId())}]selected="selected"[{/if}]>
                                        [{$oVATTBECountryVATGroup->getName()}] - [{$oVATTBECountryVATGroup->getRate()}]%
                                    </option>
                                [{/foreach}]
                            </select>
                        </td>
                    </tr>
                    [{/foreach}]
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="OEVATTBE_SAVE_BUTTON"}]" onClick="Javascript:document.myedit.fnc.value='save'">
            </td>
        </tr>
    </table>
</form>

[{*Required for admin tabs to work*}]
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
[{*/*}]