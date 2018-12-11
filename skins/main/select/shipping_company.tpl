<select name="{$name}" {if $disabled}disabled{/if}>
<option value=""{if $value eq ''} selected{/if}>{$lng.lbl_please_select}</option>
{foreach from=$shipping_companies item=sc}
<option value="{$sc.carrier_id}"{if $sc.carrier_id eq $value} selected="selected"{/if}>{$sc.carrier}</option>
{/foreach}
</select>
