[{$smarty.block.parent}]
[{if $sTBECountry}]
<tr>
    <td class="edittext" colspan="2">
        <br>
        <table style="border : 1px #A9A9A9; border-style : solid solid solid solid; padding-top: 5px; padding-bottom: 5px; padding-right: 5px; padding-left: 5px; width: 600px;">
            <tbody>
                <tr>
                    <td class="edittext" colspan="2">
                        Country evidences for TBE services
                    </td>
                </tr>
                <tr>
                    <td class="edittext">Order country</td>
                    <td class="edittext">[{$sTBECountry}]</td>
                </tr>
                [{foreach from=$aCountriesByEvidences item=evidence}]
                    <tr>
                        <td class="edittext">Country calculated by evidence [{$evidence.name}]</td>
                        <td class="edittext">[{$evidence.countryTitle}]</td>
                    </tr>
                [{/foreach}]
            </tbody>
        </table>
    </td>
</tr>
[{/if}]