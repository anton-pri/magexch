<select name="{$name}">
<option value="0"{if $value eq 0} selected="selected"{/if}>{$lng.lbl_absolute}</option>
<option value="1"{if $value eq 1} selected="selected"{/if}>{$lng.lbl_percent}</option>
</select>
