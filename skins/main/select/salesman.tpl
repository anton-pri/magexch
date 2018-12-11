{if !$salesmen}
{tunnel func='cw_salesman_get_list_smarty' load='salesman' assign='salesmen'}
{/if}
<select name="{$name}"{if $disabled} disabled{/if}{if $multiple} multiple="{$multiple}"{/if}>
{if !$multiple}
<option value="">{$lng.lbl_please_select}</option>
{/if}
{foreach from=$salesmen item=salesman}
{assign var='id' value=$salesman.customer_id}
<option value="{$id}" {if (!$multiple && $id eq $value) || ($multiple && $value.$id)} selected{/if}>{$salesman.customer_id|user_title:'B'}</option>
{/foreach}
</select>
