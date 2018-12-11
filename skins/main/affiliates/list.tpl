{if $affiliates}
{math assign="next_level" equation="x+1" x=$level}
{count assign="count" value=$affiliates}
{math assign="count" equation="x-1" x=$count|default:0}

<table cellspacing="0" cellpadding="0" width="100%">

{foreach from=$affiliates item=v key=k}
{math assign="level_delta" equation="y-x+1" x=$parent_affiliate.level y=$v.level}
<tr>
	{if $type eq 1}
	<td class="AffiliateCell{if $k < $count}BG{/if}">
	{if $k >= $count}
	<img src="{$ImagesDir}/tree_end.gif" width="19" alt="" />
	{else}
	<img src="{$ImagesDir}/tree_point.gif" width="19" alt="" />
	{/if}
	</td>
	{/if}
	{if $type eq 1}
	<td nowrap="nowrap">
	&nbsp;
	{if $usertype ne 'B'}
	<a href="index.php?target=user_modify&user={$v.customer_id|escape:"url"}&amp;usertype=B"{if $level_delta <= $config.Salesman.salesman_max_level} style="font-weight: bold;"{/if}>{$v.firstname} {$v.lastname}</a>
	{else}
	{if $level_delta <= $config.Salesman.salesman_max_level}<b>{/if}
	{$v.firstname} {$v.lastname} (level: {$level_delta})
	{if $level_delta <= $config.Salesman.salesman_max_level}</b>{/if}
	{/if}
	</td>
	{elseif $type eq 2}
	<td nowrap="nowrap" align="right" valign="middle">{include file='common/currency.tpl' value=$v.sales|default:0}</td>
	{elseif $type eq 3}
	<td nowrap="nowrap" align="right" valign="middle">{include file='common/currency.tpl' value=$v.childs_sales}</td>
	{else}
	<td nowrap="nowrap"> </td>
	{/if}
</tr>
{if $v.childs ne ''}
<tr>
	{if $type eq 1}
	<td class="AffiliateCell{if $k < $count}BG{/if}">{if $k >= $count}<img src="{$ImagesDir}/spacer.gif" width="19" alt="" />{/if}</td>
	{/if}
	<td colspan="2">{include file="main/affiliate_list.tpl" affiliates=$v.childs level=$next_level type=$type}</td>
</tr>
{/if}
{/foreach}

</table>
{/if}
