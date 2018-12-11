<select name="{$name}">
<option value="0"{if $value ne 1} selected{/if}>{$lng.lbl_open}</option>
<option value="1"{if $value eq 1} selected{/if}>{$lng.lbl_close}</option>
</select>
