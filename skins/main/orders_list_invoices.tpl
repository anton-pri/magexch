{if $orders ne ""}
{capture name=section}

<div align="right">{include file='buttons/button.tpl' button_title=$lng.lbl_search_again href=$script}</div>

<form action="index.php?target=process_{if $is_ship}ship{elseif $is_invoice}invoice{/if}" method="post" name="processorderform">
<input type="hidden" name="action" value="" />

{include file="common/navigation.tpl"}

<table cellpadding="2" cellspacing="1" width="100%">

{assign var="colspan" value=6}

<tr class="TableHead">
    <td width="5">&nbsp;</td>
    <td width="5%" nowrap="nowrap">
        {if $search_prefilled.sort_field eq "display_id"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}
<a href="{$script}&mode=search&amp;sort=display_id">
        {if $is_ship}{$lng.lbl_ship_id}
        {elseif $is_invoice}{$lng.lbl_invoice_id}
        {/if}
</a>
    </td>
	<td width="5%" nowrap="nowrap">{if $search_prefilled.sort_field eq "main_display_id"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$script}&mode=search&amp;sort=main_display_id">{$lng.lbl_doc_id}</a></td>
    <td width="5%" nowrap="nowrap">{if $search_prefilled.sort_field eq "company_name"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$script}&mode=search&amp;sort=company_name">{$lng.lbl_company_name}</a></td>
	<td width="30%" nowrap="nowrap">{if $search_prefilled.sort_field eq "customer"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$script}&mode=search&amp;sort=customer">{$lng.lbl_customer}</a></td>
{if $usertype eq "A" and $single_mode eq ""}
{assign var="colspan" value=7}
	<td width="20%" nowrap="nowrap">{if $search_prefilled.sort_field eq "warehouse"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$script}&mode=search&amp;sort=warehouse">{$lng.lbl_warehouse}</a></td>
{/if}
	<td width="20%" nowrap="nowrap">{if $search_prefilled.sort_field eq "date"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$script}&mode=search&amp;sort=date">{$lng.lbl_date}</a></td>
	<td width="20%" align="right" nowrap="nowrap">{if $search_prefilled.sort_field eq "total"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$script}&mode=search&amp;sort=total">{$lng.lbl_total}</a></td>
{if $is_invoice}
	<td width="20%" align="right" nowrap="nowrap">{$lng.lbl_payment}</td>
{/if}
</tr>

{section name=oid loop=$orders}
<tr {cycle values=", class='cycle'"}
    <td width="5"><input type="checkbox" name="doc_ids[{$orders[oid].doc_id}]" /></td>
    <td nowrap><a href="{$detail_script}&doc_id={$orders[oid].doc_id}">{if $is_ship}{$lng.lbl_ship_id}{elseif $is_invoice}{$lng.lbl_invoice_id}{/if} {$orders[oid].display_id}</a></td>
	<td align="center"><a href="{$detail_script}&doc_id={$orders[oid].doc_id}">{$orders[oid].order_display_id}</a></td>
    <td>{$orders[oid].company_name}</td>
	<td>{if $orders[oid].usertype eq 'R'}{$orders[oid].company}{else}{$orders[oid].firstname} {$orders[oid].lastname} ({$orders[oid].customer_id}){/if}</td>
{if $usertype eq "A" and $single_mode eq ""}
	<td>{$orders[oid].warehouse_title}</td>
{/if}
	<td nowrap="nowrap"><a href="{$detail_script}&doc_id={$orders[oid].doc_id}">{$orders[oid].date|date_format:$config.Appearance.datetime_format}</a></td>
	<td nowrap="nowrap" align="right">
	<a href="{$detail_script}&doc_id={$orders[oid].doc_id}">{include file='common/currency.tpl' value=$orders[oid].total}</a>
	</td>
{if $is_invoice}
        <td>
        {if $orders[oid].status eq 'E'}<font class="ErrorMessage">{/if}{include file="main/invoice_status.tpl" order=$orders[oid] extended=true}
        {if $orders[oid].status eq 'E'}</font>{/if}
        </td>
{/if}
</tr>

{/section}

<tr>
	<td colspan="{$colspan}"><img src="{$ImagesDir}/spacer.gif" width="100%" height="1" alt="" /></td>
</tr>
<tr>
	<td colspan="{$colspan}">
{include file="common/navigation.tpl"}
    </td>
</tr>

</table>

<input type="button" value="{if $is_ship}{$lng.lbl_print_ship_for_selected}{elseif $is_invoice}{$lng.lbl_print_invoices_for_selected}{/if}" onclick="javascript: if (checkMarks(this.form, new RegExp('doc_ids\[[0-9]+\]', 'gi'))) {ldelim} document.processorderform.target='invoices'; cw_submit_form(this, 'invoice'); document.processorderform.target=''; {rdelim}" />

{if $usertype eq "A" or $usertype eq "P"}
<script>
var txt_delete_selected_orders_warning = "{if $is_ship}{$lng.txt_delete_selected_ship_warning|escape:javascript|strip_tags}{elseif $is_invoice}{$lng.txt_delete_selected_invoices_warning|escape:javascript|strip_tags}{/if}";
</script>
{if ($is_ship && $accl.102101) || ($is_invoice && $accl.102001)}
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('doc_ids\[[0-9]+\]', 'gi'))) if (confirm(txt_delete_selected_orders_warning)) cw_submit_form(this, 'delete');" />
{/if}
{/if}
</form>

{/capture}
{include file="common/section.tpl" title=$lng.lbl_search_results content=$smarty.capture.section extra='width="100%"'}
{/if}
