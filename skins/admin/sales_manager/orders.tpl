{include file='common/page_title.tpl' title=$lng.lbl_salesmans_orders}
{$lng.txt_salesman_orders_note}<br /><br />

<!-- IN THIS SECTION -->


<br />

<!-- IN THIS SECTION -->

{include file="common/navigation.tpl"}
{assign var="found" value="N"}
{capture name=section}
<form method="post" action="index.php?target=salesman_orders" name="searchform">
<input type="hidden" name="action" value="" />

<table>
<tr>
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_date_from}:</td>
	<td height="10" width="10">&nbsp;</td>
	<td nowrap="nowrap">{html_select_date prefix="Start" time=$search.start_date|default:$month_begin start_year=$config.Company.start_year end_year=$config.Company.end_year}</td>
</tr>
<tr>
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_date_through}:</td>
	<td height="10" width="10">&nbsp;</td>
	<td nowrap="nowrap">{html_select_date prefix="End" time=$search.end_date start_year=$config.Company.start_year end_year=$config.Company.end_year}</td>
</tr>
<tr>
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_order_id}:</td>
	<td height="10" width="10">&nbsp;</td>
	<td nowrap="nowrap"><input type="text" size="8" name="search[doc_id]" value="{$search.doc_id}" /></td>
</tr>
<tr>
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_salesman}:</td>
	<td height="10" width="10">&nbsp;</td>
	<td nowrap="nowrap">
	<select name="search[customer_id]">
	<option value=''{if $search.login eq ''} selected="selected"{/if}>{$lng.lbl_all}</option>
	{if $salesmans ne ''}
	{foreach from=$salesmans item=v}
		<option value="{$v.customer_id}"{if $search.customer_id eq $v.customer_id} selected="selected"{/if}>#{$v.customer_id} {$v.email}</option>
	{/foreach}
	{/if}
	</select>
	</td>
</tr>
<tr>
    <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_order_status}</td>
    <td height="10" width="10">&nbsp;</td>
    <td nowrap="nowrap">
	<select name="search[status]">
		<option value=""{if $search.status eq ""} selected="selected"{/if}>{$lng.lbl_all}</option>
		<option value="I"{if $search.status eq "I"} selected="selected"{/if}>{$lng.lbl_not_finished}</option>
		<option value="Q"{if $search.status eq "Q"} selected="selected"{/if}>{$lng.lbl_queued}</option>
		<option value="P"{if $search.status eq "P"} selected="selected"{/if}>{$lng.lbl_processed}</option>
		<option value="B"{if $search.status eq "B"} selected="selected"{/if}>{$lng.lbl_backordered}</option>
		<option value="D"{if $search.status eq "D"} selected="selected"{/if}>{$lng.lbl_declined}</option>
		<option value="F"{if $search.status eq "F"} selected="selected"{/if}>{$lng.lbl_failed}</option>
		<option value="C"{if $search.status eq "C"} selected="selected"{/if}>{$lng.lbl_complete}</option>
	</select>
	</td>
</tr>
<tr>
    <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_payment_status}</td>
    <td height="10" width="10">&nbsp;</td>
    <td nowrap="nowrap">
	<select name="search[paid]">
		<option value=''{if $search.paid eq ''} selected="selected"{/if}>{$lng.lbl_all}</option>
	    <option value='N'{if $search.paid eq 'N'} selected="selected"{/if}>{$lng.lbl_pending}</option>
		<option value='A'{if $search.paid eq 'A'} selected="selected"{/if}>{$lng.lbl_approved}</option>
	    <option value='Y'{if $search.paid eq 'Y'} selected="selected"{/if}>{$lng.lbl_paid}</option>
    </select>
	</td>
</tr>
<tr>
	<td height="10" class="FormButton">{$lng.lbl_csv_delimiter}:</td>
	<td height="10" width="10">&nbsp;</td>
	<td height="10">{include file='main/select/delimiter.tpl'}</td>
</tr>

<tr>
	<td colspan="3" class="SubmitBox">
	<input type="button" value="{$lng.lbl_search|strip_tags:false|escape}" onclick="javascript: cw_submit_form(this, 'go');" />
	<input type="button" value="{$lng.lbl_export|strip_tags:false|escape}" onclick="javascript: cw_submit_form(this, 'export');" />
	</td>
</tr>

</table>
</form>

{$lng.txt_salesman_orders_bottom}
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_search extra='width="100%"'}

<br />

{if $orders ne ''}
{capture name=section}
<table cellpadding="2" cellspacing="2" width="100%">
<tr class="TableHead">
	<td nowrap="nowrap" rowspan="2">{$lng.lbl_salesman}</td>
    <td nowrap="nowrap" colspan="2" align="center">{$lng.lbl_order}</td>
    <td nowrap="nowrap" rowspan="2" align="center">{$lng.lbl_total}</td>
    <td nowrap="nowrap" rowspan="2" align="center">{$lng.lbl_commission}</td>
    <td nowrap="nowrap" rowspan="2" align="center">{$lng.lbl_owner}</td>
    <td nowrap="nowrap" colspan="2" align="center">{$lng.lbl_status}</td>
</tr>
<tr class="TableHead">
    <td nowrap="nowrap" align="center">#</td>
    <td nowrap="nowrap" align="center">{$lng.lbl_date}</td>
    <td nowrap="nowrap" align="center">{$lng.lbl_order}</td>
    <td nowrap="nowrap" align="center">{$lng.lbl_commission}</td>
</tr>
{foreach from=$orders item=v}
<tr>
	<td><a href="index.php?target=user_modify&user={$v.customer_id|escape:"url"}&amp;usertype=B">#{$v.customer_id} {$v.email}</a></td>
    <td><a href="index.php?target=docs_O&doc_id={$v.doc_id}">{$v.display_id}</a></td>
	<td nowrap="nowrap">{$v.date|date_format:$config.Appearance.date_format}</td>
	<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$v.subtotal}</td>
	<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$v.commissions}</td>
	<td nowrap="nowrap">{if $v.affiliate ne ''}{$lng.lbl_child} ({$v.affiliate}){else}{$lng.lbl_affiliate}{/if}</td>
	<td>{include file="main/select/doc_status.tpl" status=$v.order_status mode="static" name="status"}</td>
	<td>{if $v.paid eq 'Y'}{$lng.lbl_paid}{elseif $v.paid eq 'A'}{$lng.lbl_approved}{else}{$lng.lbl_pending}{/if}</td>
</tr>
{/foreach}
</table>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_salesmans_orders extra='width="100%"'}
{/if}
