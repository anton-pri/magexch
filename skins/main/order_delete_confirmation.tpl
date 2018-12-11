    {if $is_invoice}
{include file='common/page_title.tpl' title=$lng.lbl_delete_invoices}
    {elseif $is_ship}
{include file='common/page_title.tpl' title=$lng.lbl_delete_ships}
    {else}
{include file='common/page_title.tpl' title=$lng.lbl_delete_orders}
    {/if}


{capture name=section}

{if $mode ne "delete_all"}
<div align="right">{include file='buttons/button.tpl' button_title=$lng.lbl_return_to_search_results href="`$redirect_url_orders`?mode=search"}</div>
{else}
<div align="right">{include file='buttons/button.tpl' button_title=$lng.lbl_go_back href=$redirect_url_orders}</div>
{/if}
<br />

<form action="{$redirect_url}" method="post" name="processform">
<input type="hidden" name="action" value="" />
<input type="hidden" name="confirmed" value="Y" />

<span class="Text">
    {if $is_invoice}
{$lng.lbl_invoice_delete_confirmation_header}
    {elseif $is_ship}
{$lng.lbl_ship_delete_confirmation_header}
    {else}
{$lng.lbl_order_delete_confirmation_header}
    {/if}
</span>

<br /><br />

{if $mode eq "delete_all"}

<dl>
<dd>{$lng.txt_delete_N_orders_message|substitute:"count":$orders_count}</dd>
</dl>

{else}

<ul>
{section name=oid loop=$orders}
<li><span class="ProductPriceSmall">
    {if $is_invoice}
{$lng.lbl_invoice}
    {elseif $is_ship}
{$lng.lbl_ship_doc}
    {else}
{$lng.lbl_order}
    {/if}
 #{$orders[oid].display_id} - {include file='common/currency.tpl' value=$orders[oid].total}
</span>
<dl>
<dd>{$lng.lbl_date}: {$orders[oid].date|date_format:$config.Appearance.date_format}</dd>
<dd>{$lng.lbl_status}: {include file="main/select/doc_status.tpl" status=$orders[oid].status mode="static"}</dd>
{if $orders[oid].warehouse_title}
<dd>{$lng.lbl_warehouse}: {$orders[oid].warehouse_title}</dd>
{/if}
</dl>
</li>
{/section}
</ul>

<br />

{/if}

{$lng.txt_operation_not_reverted_warning}

<br /><br />
{if $mode ne "delete_all"}
{assign var="button_href" value="delete"}
{assign var="button_href_no" value="&mode=search"}
{else}
{assign var="button_href" value="delete_all"}
{/if}
<table cellspacing="0" cellpadding="0">
<tr>
	<td>{$lng.txt_are_you_sure_to_proceed}</td>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td>{include file="buttons/yes.tpl" href="javascript: document.processform.mode.value='`$button_href`'; cw_submit_form(document.processform)"}</td>
	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td>{include file="buttons/no.tpl" href="index.php?target=orders`$button_href_no`"}</td>
</tr>
</table>

</form>

{/capture}
{include file="common/section.tpl" title=$lng.lbl_confirmation content=$smarty.capture.section extra='width="100%"'}
