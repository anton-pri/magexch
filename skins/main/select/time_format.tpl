{select_time assign="time_formats"}
<select name="{$name}"{if $is_disabled} disabled{/if}>
{foreach from=$time_formats item=date}
<option value="{$date}"{if $date eq $value} selected{/if}>{$date}</option>
{/foreach}
</select>
