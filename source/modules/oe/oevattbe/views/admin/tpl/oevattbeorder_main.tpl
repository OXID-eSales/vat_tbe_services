[{$smarty.block.parent}]
[{if $sTBECountry}]
<tr>
    <td class="edittext" colspan="2">
        <br>
        <table style="border : 1px #A9A9A9; border-style : solid solid solid solid; padding-top: 5px; padding-bottom: 5px; padding-right: 5px; padding-left: 5px; width: 600px;">
            <thead>
                <tr>
                    <td class="edittext" colspan="2">
                        Country evidences for TBE services
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="edittext" colspan="2">For order TBE products used [{$sTBECountry}] VAT rates</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table border="1" style="border-collapse:collapse; width: 80%" cellpadding="2">
                            <tr>
                                <th class="edittext">Evidence Id</th>
                                <th class="edittext">Country</th>
                            </tr>
                            [{foreach from=$aEvidencesData item=evidence}]
                            <tr>
                                <td class="edittext">[{$evidence.name}]</td>
                                <td class="edittext">[{$evidence.countryTitle}]</td>
                            </tr>
                            [{/foreach}]
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>
[{/if}]