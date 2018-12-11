{if $disabled}
{if $memberships}
{if (!$multiple && 0 eq $value) || ($multiple && $value.0)}{$lng.lbl_retail_level} {/if}
{foreach from=$memberships item=membership}
    {assign var="key" value=$membership.membership_id}
    {if (!$multiple && $key eq $value) || ($multiple && $value.$key)}{$membership.membership} {/if}
{/foreach}
{/if}
{else}
<select name="{$name}" id="{$name|id}"{if $multiple} multiple size="5"{/if} class="form-control" >
{if $is_please_select}
<option value="">{$lng.lbl_retail_level}</option>
{/if}
{if $memberships}
{foreach from=$memberships item=membership}
{assign var="key" value=$membership.membership_id}
<option value="{$key}"{if (!$multiple && $key eq $value) || ($multiple && $value.$key)} selected="selected"{/if}>{$membership.membership}</option>
{/foreach}
{/if}
</select>
{/if}
