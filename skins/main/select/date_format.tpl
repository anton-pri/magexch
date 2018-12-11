{select_date assign="date_formats"}
<select name="{$name}">
{foreach from=$date_formats item=date}
<option value="{$date}"{if $date eq $value} selected{/if}>{$date}</option>
{/foreach}
</select>
