{if $url eq '' && $navigation.script ne ''}{assign var="url" value=$navigation.script|replace:"&":"&amp;"|cat:"&amp;"}{elseif $url ne ''}{assign var="url" value=$url|amp}{/if}
<table cellspacing="0" cellpadding="0">
<tr>
	<td class="SearchSortTitle">{$lng.lbl_sort_by}:</td>
{foreach from=$sort_fields key=name item=field}
	{assign var="cur_url" value=$url|cat:"sort="|cat:$name|cat:"&amp;sort_direction="}
	{if $name eq $selected}
	<td><a class="SearchSortLink" href="{$cur_url}{if $direction eq 1}0{else}1{/if}" title="{$lng.lbl_sort_by|escape}: {$field}"><img src="{$ImagesDir}/{if $direction}darrow.gif{else}uarrow.gif{/if}" class="SearchSortImg" alt="{$lng.lbl_sort_direction|escape}" /></a></td>
	{/if}
	<td class="SearchSortCell"><a class="SearchSortLink" href="{$cur_url}{if $name eq $selected}{if $direction eq 1}0{else}1{/if}{else}{$direction}{/if}" title="{$lng.lbl_sort_by|escape}: {$field}">{if $name eq $selected}<b>{/if}{$field}{if $name eq $selected}</b>{/if}</a></td>
{/foreach}
</tr>
</table>
