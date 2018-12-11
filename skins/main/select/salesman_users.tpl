<select name="{$name}"{if $disabled} disabled{/if}>
<option value="">{$lng.lbl_please_select}</option>
{foreach from=$salesman_users item=user}
<option value="{$user.customer_id}"{if $user.customer_id eq $value} selected="selected"{/if}>{$user.customer_id|user_title:$user.usertype}</option>
{/foreach}
</select>
