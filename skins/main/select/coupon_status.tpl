<select name="{$name}">
    <option value="1"{if $value eq 1} selected="selected"{/if}>{$lng.lbl_coupon_active}</option>
    <option value="0"{if $value eq 0} selected="selected"{/if}>{$lng.lbl_coupon_disabled}</option>
    <option value="2"{if $value eq 2} selected="selected"{/if}>{$lng.lbl_coupon_used}</option>
</select>
