<select name="{$name}">
{foreach from=$reasons item=v}
<option value='{$v.reason_id}' {if $value eq $v.reason_id}selected{/if}>{$v.title}</option>
{/foreach}
</select>
