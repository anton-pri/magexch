{tunnel func='cw_pos_get_list_smarty' load='pos' assign='pos_users'}
<select name="{$name}"{if $disabled} disabled{/if}>
<option value="">{$lng.lbl_please_select}</option>
{if $pos_users}
{foreach from=$pos_users item=pu}
<option value="{$pu.customer_id}"{if $pu.customer_id eq $value} selected="selected"{/if}>{$pu.firstname} {$pu.lastname}</option>
{/foreach}
{/if}
</select>
