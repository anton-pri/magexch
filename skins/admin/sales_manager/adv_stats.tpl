<div class="dialog_title">{$lng.txt_advertising_stats_note}</div>
 
{capture name=section}
<form action="index.php?target=salesman_adv_stats" method="post">

<div class="input_field_0">
	<label>{$lng.lbl_period_from}</label>
	{html_select_date prefix="Start" time=$search.start_date|default:$month_begin start_year=$config.Company.start_year end_year=$config.Company.end_year}
</div>
<div class="input_field_0">
    <label>{$lng.lbl_period_to}</label>
    {html_select_date prefix="End" time=$search.end_date start_year=$config.Company.start_year end_year=$config.Company.end_year}
</div>
<div class="input_field_0">
    <label>{$lng.lbl_campaigns}</label>
    <select name="search[campaign_id]">
	<option value=''{if $search.campaign_id eq ''} selected="selected"{/if}>{$lng.lbl_all}</option>
	{if $campaigns ne ''}
	{foreach from=$campaigns item=v}
	<option value='{$v.campaign_id}'{if $search.campaign_id eq $v.campaign_id} selected="selected"{/if}>{$v.campaign}</option>
	{/foreach}
	{/if}
	</select>
</div>

<input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" />
</form>

{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_search}

{if $result ne ''}
{capture name=section}
<table class="header" width="100%">
<tr>
	<th>{$lng.lbl_campaign}</th>
    <th>{$lng.lbl_clicks}</th>
    <th>{$lng.lbl_estimated_expences}</th>
    <th>{$lng.lbl_acquisition_cost}</th>
	<th>{$lng.lbl_sales}</th>
	<th>{$lng.lbl_roi}</th>
</tr>
{foreach from=$result item=v}
<tr>
	<td><a href="index.php?target=salesman_adv_campaigns&campaign_id={$v.campaign_id}">{$v.campaign}</a></td>
	<td>{$v.clicks}</td>
    <td>{include file='common/currency.tpl' value=$v.ee|default:"0"}</td>
	<td>{include file='common/currency.tpl' value=$v.acost|default:"0"}</td>
    <td>{include file='common/currency.tpl' value=$v.total|default:"0"}</td>
    <td>{$v.roi|default:"0"}%</td>
</tr>
{/foreach}
<tr>
	<th>{$lng.lbl_total}</th>
    <td>{$total.clicks}</td>
    <td>{include file='common/currency.tpl' value=$total.ee|default:"0"}</td>
    <td>{include file='common/currency.tpl' value=$total.acost|default:"0"}</td>
    <td>{include file='common/currency.tpl' value=$total.total|default:"0"}</td>
    <td>{$total.roi|default:"0"}%</td>
</tr>
</table>

{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_advertising_campaigns}
{/if}
