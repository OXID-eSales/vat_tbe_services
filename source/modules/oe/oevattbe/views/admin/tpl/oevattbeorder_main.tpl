[{$smarty.block.parent}]
[{if $sTBECountry}]
<style>
    .vattbeEvidences {
        border: solid 1px #A9A9A9;
        padding: 5px;
        width: 600px;
        margin-top: 14px;
    }
    .vattbeEvidencesInfo {
        border-collapse:collapse;
        width: 80%
    }
</style>
<tr>
    <td class="edittext" colspan="2">
        <table class="vattbeEvidences">
            <thead>
                <tr>
                    <td class="edittext" colspan="2">
                        [{oxmultilang ident="OEVATTBE_TITLE_EVIDENCES_FOR_TBE"}]
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="edittext" colspan="2">[{oxmultilang ident="OEVATTBE_EVIDENCE_FOR_ARTICLES_USED" args=$sTBECountry}]</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table border="1" class="vattbeEvidencesInfo" cellpadding="2">
                            <tr>
                                <th class="edittext">[{oxmultilang ident="OEVATTBE_EVIDENCE_ID"}]</th>
                                <th class="edittext">[{oxmultilang ident="OEVATTBE_EVIDENCE_COUNTRY"}]</th>
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