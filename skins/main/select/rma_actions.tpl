<select name="{$name}">
{foreach from=$actions item=v}
<option value='{$v.action_id}' {if $value eq $v.action_id}selected{/if}>{$v.title}</option>
{/foreach}
</select>
