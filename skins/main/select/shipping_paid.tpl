{if $flat}
    {if $value eq 1}{$lng.lbl_shipping_paid_sender}{elseif $value eq 2}{$lng.lbl_shipping_paid_receiver}{/if}
{else}
<select name="{$name}" {if $disabled}disabled{/if}>
<option value="1"{if $value eq 1} selected{/if}>{$lng.lbl_shipping_paid_sender}</option>
<option value="2"{if $value eq 2} selected{/if}>{$lng.lbl_shipping_paid_receiver}</option>
</select>
{/if}
