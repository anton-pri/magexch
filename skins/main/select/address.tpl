{tunnel func='cw_user_get_addresses_smarty' customer_id=$customer_id assign='addresses'}
<select name="{$name}"{if $onchange} onchange="{$onchange}"{/if}{if $disabled} disabled{/if}>
<option value="">{$lng.lbl_please_select}</option>
{foreach from=$addresses item=address}
<option value="{$address.address_id}"{if $address.address_id eq $value} selected="selected"{/if}>{$address.address}{if $address.address_2} {$address.address_2}{/if} {$address.city} {$address.state} {$address.country}</option>
{/foreach}
</select>
