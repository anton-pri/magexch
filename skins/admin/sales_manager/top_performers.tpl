{include file='common/page_title.tpl' title=$lng.lbl_top_performers}
{$lng.txt_top_performers_note}<br /><br />

<!-- IN THIS SECTION -->



<!-- IN THIS SECTION -->
<br />
 
{capture name=section}
<form action="index.php?target=salesman_top_performers" method="post">

<table>
<tr>
	<td>{$lng.lbl_period_from}:</td>
	<td>{html_select_date prefix="Start" time=$search.start_date|default:$month_begin start_year=$config.Company.start_year end_year=$config.Company.end_year}</td>
</tr>
<tr>
    <td>{$lng.lbl_period_to}:</td>
    <td>{html_select_date prefix="End" time=$search.end_date start_year=$config.Company.start_year end_year=$config.Company.end_year}</td>
</tr>
<tr>
    <td>{$lng.lbl_report_by}:</td>
    <td>
	<select name="search[report]">
		<option value='login'{if $search.report eq 'login'} selected="selected"{/if}>{$lng.lbl_affiliates}</option>
	    <option value='referer'{if $search.report eq 'referer'} selected="selected"{/if}>{$lng.lbl_referrer}</option>
	</select>
	</td>
</tr>
<tr>
    <td>{$lng.lbl_sort_by}:</td>
    <td>
	<select name="search[sort]">
    	<option value='clicks'{if $search.sort eq 'clicks'} selected="selected"{/if}>{$lng.lbl_clicks}</option>
	    <option value='sales'{if $search.sort eq 'sales'} selected="selected"{/if}>{$lng.lbl_sales}</option>
    </select>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td class="SubmitBox"><input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" /></td>
</tr>
</table>
</form>

{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_search extra='width="100%"'}

<br />

{if $result ne ''}
{capture name=section}
<table cellspacing="1" cellpadding="2">
<tr class="TableHead">
	<td>{if $search.report eq 'login'}{$lng.lbl_affiliates}{else}{$lng.lbl_referrer}{/if}</td>
    <td>{$lng.lbl_clicks}</td>
    <td>{$lng.lbl_sales_number}</td>
    <td>{$lng.lbl_sales}</td>
</tr>
{foreach from=$result item=v}
<tr>
	<td>{$v.name|default:$lng.lbl_unknown}</td>
	<td>{$v.clicks}</td>
	<td>{$v.num_sales}</td>
    <td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$v.sales|default:"0"}</td>
</tr>
{/foreach}
</table>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_top_performers extra='width="100%"'} 
{/if}
