<select name="{$name}">
<option value="1"{if $value eq 1} selected{/if}>{$lng.lbl_selling_price}</option>
<option value="2"{if $value eq 2} selected{/if}>{$lng.lbl_list_price}</option>
<option value="3"{if $value eq 3} selected{/if}>{$lng.lbl_selling_price} ({$lng.lbl_no_discount})</option>
<option value="4"{if $value eq 4} selected{/if}>{$lng.lbl_list_price} ({$lng.lbl_no_discount})</option>
</select>
