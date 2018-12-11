<select name="{$name}" id="{$name|id}">
    <option value="Y"{if $value eq "Y"} selected="selected"{/if}>{$lng.lbl_account_status_enabled}</option>
    <option value="N"{if $value eq "N"} selected="selected"{/if}>{$lng.lbl_account_status_suspended}</option>
    {if $addons.Salesman and $usertype eq "B"}
    <option value="Q"{if $value eq "Q"} selected="selected"{/if}>{$lng.lbl_account_status_not_approved}</option>
    <option value="D"{if $value eq "D"} selected="selected"{/if}>{$lng.lbl_account_status_declined}</option>
    {/if}
</select>
<div id="{$status_note|id}" style="margin-top: 10px;">
<label>{$lng.lbl_note_4_admin}:</label>
<textarea cols="65" rows="2" name="{$status_note}">{$value_note|escape:"html"}</textarea>
</div>
