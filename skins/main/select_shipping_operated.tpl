<select name="{$name}" {if $disabled}disabled{/if}>
<option value="1"{if $value eq 1} selected{/if}>{$lng.lbl_shipping_operated_sender}</option>
<option value="2"{if $value eq 2} selected{/if}>{$lng.lbl_shipping_operated_dest}</option>
<option value="3"{if $value eq 3} selected{/if}>{$lng.lbl_shipping_operated_company}</option>
</select>
