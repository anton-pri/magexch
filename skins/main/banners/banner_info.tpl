<div class="dialog_title">{$lng.txt_banner_stats_note}</div>

{capture name=section}
<form action="index.php?target=banner_info" method="post">
<table>
<tr>
	<td>{$lng.lbl_date}:</td>
	<td>
        {include file='main/select/date.tpl' name='posted_data[start_date]' value=$search.start_date}
        {include file='main/select/date.tpl' name='posted_data[end_date]' value=$search.end_date}
    </td>
</tr>
{if $usertype eq 'A' || ($usertype eq 'P' && $addons.Simple_Mode ne '')}
<tr>
    <td>{$lng.lbl_salesman}:</td>
    <td>
	<select name="search[salesman]">
		<option value=''{if $search.salesman eq ''} selected="selected"{/if}>{$lng.lbl_all}</option>
	{if $salesmans ne ''}
	{foreach from=$salesmans item=v}
		<option value='{$v.customer_id}'{if $search.salesman eq $v.customer_id} selected="selected"{/if}>#{$v.customer_id} {$v.email} ({$v.firstname} {$v.lastname})</option>
	{/foreach}
	{/if}
	</select>
	</td>
</tr>
{/if}
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" /></td>
</tr>
</table>
</form>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_search extra='width="100%"'}

{capture name=section}
<table class="header" width="100%">
<tr>
	<th>{$lng.lbl_banner}</th>
	<th>{$lng.lbl_clicks}</th>
	<th>{$lng.lbl_views}</th>
	<th>{$lng.lbl_click_rate}</th>
</tr>
{if $banners}
{foreach from=$banners item=v}
<tr>
	<td>{if $v.banner_id > 0}{if $usertype ne 'B' && $v.banner}<a href="index.php?target=salesman_banners&banner_id={$v.banner_id}">{/if}{$v.banner|default:$lng.lbl_deleted_banner}{if $usertype ne 'B' && $v.banner}</a>{/if}{else}{$lng.lbl_default_banner}{/if}{if $v.product_id > 0} ({$lng.lbl_product}: <a href="index.php?target=products&mode=details&product_id={$v.product_id}">{$v.product|truncate:50}</a>){if $v.class eq 1}, {$lng.lbl_detailed}{elseif $v.class eq 2}, {$lng.lbl_normal}{elseif $v.class eq 3}, {$lng.lbl_compact}{/if}{/if}</td>
    <td align="right">{$v.clicks}</td>
    <td align="right">{$v.views}</td>
	<td align="right">{$v.click_rate|formatprice}</td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="4" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
<tr>
	<td><b>{$lng.lbl_total}:</b></td>
    <td align="right">{$total.clicks}</td>
    <td align="right">{$total.views}</td>
    <td align="right">{$total.click_rate|formatprice}</td>
</tr>
</table>
{/capture}
{include file='common/section.tpl' content=$smarty.capture.section title=$lng.lbl_banners_statistics}
