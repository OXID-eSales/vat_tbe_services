<td valign="top" class="listfilter first" align="center">
    <div class="r1">
        <div class="b1">
            <input class="listedit" type="text" size="3" maxlength="128" name="where[oxcountry][oxactive]" value="[{ $where.oxcountry.oxactive }]">
        </div>
    </div>
</td>
<td valign="top" class="listfilter">
    <div class="r1">
        <div class="b1">
            <input class="listedit" type="text" size="50" maxlength="128" name="where[oxcountry][oxtitle]" value="[{ $where.oxcountry.oxtitle }]">
        </div>
    </div>
</td>
<td valign="top" class="listfilter">
    <div class="r1">
        <div class="b1">
            <input class="listedit" type="text" size="50" maxlength="128" name="where[oxcountry][oxshortdesc]" value="[{ $where.oxcountry.oxshortdesc }]">
        </div>
    </div>
</td>
<td valign="top" class="listfilter">
    <div class="r1">
        <div class="b1">
            <input class="listedit" type="text" size="3" maxlength="128" name="where[oxcountry][oevattbe_appliestbevat]" value="[{ $where.oxcountry.oevattbe_appliestbevat }]">
        </div>
    </div>
</td>
<td valign="top" class="listfilter" colspan="2">
    <div class="r1">
        <div class="b1">
            <div class="find">
                <select name="changelang" class="editinput" onChange="Javascript:top.oxid.admin.changeLanguage();">
                    [{foreach from=$languages item=lang}]
                        <option value="[{ $lang->id }]" [{ if $lang->selected}]SELECTED[{/if}]>[{ $lang->name }]</option>
                    [{/foreach}]
                </select>
                <input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]">
            </div>

            <input class="listedit" type="text" size="5" maxlength="128" name="where[oxcountry][oxisoalpha3]" value="[{ $where.oxcountry.oxisoalpha3 }]">
        </div>
    </div>
</td>
