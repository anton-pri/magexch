{if $static}
    {if $value eq 3} {$lng.lbl_coupon_pending}{/if}
    {if $value eq 1} {$lng.lbl_coupon_active}{/if}
    {if $value eq 0} {$lng.lbl_coupon_disabled}{/if}
    {if $value eq 2} {$lng.lbl_coupon_used}{/if}
{else}
<select name="{$name}">
    <option value="3"{if $value eq 3} selected="selected"{/if}>{$lng.lbl_coupon_pending}</option>
    <option value="1"{if $value eq 1} selected="selected"{/if}>{$lng.lbl_coupon_active}</option>
    <option value="0"{if $value eq 0} selected="selected"{/if}>{$lng.lbl_coupon_disabled}</option>
    <option value="2"{if $value eq 2} selected="selected"{/if}>{$lng.lbl_coupon_used}</option>
</select>
{/if}