<select name="{$name}" class="form-control">
    <option value="P"{if $value eq "P"} selected="selected"{/if}>{$lng.lbl_pending}</option>
    <option value="A"{if $value eq "A"} selected="selected"{/if}>{$lng.lbl_active}</option>
    <option value="B"{if $value eq "B"} selected="selected"{/if}>{$lng.lbl_blocked}</option>
    <option value="D"{if $value eq "D"} selected="selected"{/if}>{$lng.lbl_disabled}</option>
    <option value="E"{if $value eq "E"} selected="selected"{/if}>{$lng.lbl_expired}</option>
    <option value="U"{if $value eq "U"} selected="selected"{/if}>{$lng.lbl_used}</option>
</select>
