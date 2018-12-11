<select name="{$name}">
    <option value="%"{if $value eq "%"} selected="selected"{/if}>{$lng.lbl_persent}</option>
    <option value="$"{if $value eq "$"} selected="selected"{/if}>{$lng.lbl_flat}</option>
</select>
