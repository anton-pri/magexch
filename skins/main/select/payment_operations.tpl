<select name="{$name}" multiple="2">
<option value="1"{if $value & 1} selected{/if}>{$lng.lbl_selling}</option>
<option value="2"{if $value & 2} selected{/if}>{$lng.lbl_purchase}</option>
</select>
