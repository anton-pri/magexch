{assign var='s_total' value=0.00}
{assign var='s_tax' value=0.00}
{assign var='s_shipping_cost' value=0.00}
{assign var='s_shipping_cost_no_offer' value=0.00}
{assign var='s_cost' value=0.00}
{assign var='s_items' value=0}
{assign var='s_profit' value=0.00}
{assign var='a_total' value=0.00}
{assign var='a_tax' value=0.00}
{assign var='a_shipping_cost' value=0.00}
{assign var='a_shipping_cost_no_offer' value=0.00}
{assign var='a_cost' value=0.00}
{assign var='a_items' value=0}
{assign var='a_profit' value=0.00}

{if $orders}

<form action="index.php?target={$current_target}" method="post" name="process_order_form">
<div class="box orders">

<input type="hidden" name="action" value="" />

<table class="table table-striped dataTable" >
<thead>
<tr>
	<th width="16%">{if $search_prefilled.sort_field eq "display_doc_id"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=display_doc_id&amp;sort_direction={if $search_prefilled.sort_field eq 'display_doc_id'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">#</a></th>
	<th></th>
	<th></th>
	<th>
        {if $search_prefilled.sort_field eq 'customer'}{include file='buttons/sort_pointer.tpl' dir=$search_prefilled.sort_direction}&nbsp;{/if}
        <a href="index.php?target={$current_target}&amp;mode=search&amp;sort=customer&amp;sort_direction={if $search_prefilled.sort_field eq 'customer'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{lng name="lbl_order_customer_`$docs_type`"}</a>
       </th>
	<th>{if $search_prefilled.sort_field eq "date"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=date&amp;sort_direction={if $search_prefilled.sort_field eq 'date'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_date}</a></th>
       <th class="text-right">{if $search_prefilled.sort_field eq "total"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=total&amp;sort_direction={if $search_prefilled.sort_field eq 'total'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_total}</a></th>
       <th class="text-right">{if $search_prefilled.sort_field eq "tax"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=tax&amp;sort_direction={if $search_prefilled.sort_field eq 'tax'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_tax}</a></th>
       <th class="text-right">{if $search_prefilled.sort_field eq "shipping_cost"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=shipping_cost&amp;sort_direction={if $search_prefilled.sort_field eq 'shipping_cost'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_shipping}</a><br />real (charged)</th>
	<th class="text-right">{if $search_prefilled.sort_field eq "cost"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=cost&amp;sort_direction={if $search_prefilled.sort_field eq 'cost'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_cost}</a></th>
	<th class="text-right">{if $search_prefilled.sort_field eq "items"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=items&amp;sort_direction={if $search_prefilled.sort_field eq 'items'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_items}</a></th>
	<th class="text-right">{if $search_prefilled.sort_field eq "profit"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=profit&amp;sort_direction={if $search_prefilled.sort_field eq 'profit'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_order_profit}</a></th>
</tr>
</thead>
<tbody>
{foreach from=$orders item=order name=items}

{math equation="x + y" x=$s_total y=$order.total assign="s_total"}
{math equation="x + y" x=$s_tax y=$order.tax assign="s_tax"}
{math equation="x + y" x=$s_shipping_cost y=$order.shipping_cost assign="s_shipping_cost"}
{math equation="x + y" x=$s_shipping_cost_no_offer y=$order.extra.shipping_no_offer assign="s_shipping_cost_no_offer"}
{math equation="x + y" x=$s_cost y=$order.cost assign="s_cost"}
{math equation="x + y" x=$s_items y=$order.items assign="s_items"}
{math equation="x + y" x=$s_profit y=$order.profit assign="s_profit"}

<tr {cycle values=", class='cycle'"}>
	<td nowrap="nowrap">
		<a href="index.php?target=docs_O&mode=details&doc_id={$order.doc_id}">#{$order.display_id}</a>
		<br />({include file="main/select/doc_status.tpl" status=$order.status mode="static"})
	</td>
	<td>
		{if $order.customer_notes ne ''}
		<span class="order_tooltip" title="{$order.customer_notes}">
                     <i class="si si-info fa-15x text-info"></i>
		</span>
		{/if}
	</td>
	<td>
		{if $order.notes ne ''}
		<span class="order_tooltip" title="{$order.notes}">
                     <i class="si si-info fa-15x text-danger"></i>
		</span>
		{/if}
	</td>
	<td class="customer_column">
        {$order.customer_id|user_title:$order.usertype:$order.doc_id}
    </td>
    <td nowrap="nowrap"><a href="index.php?target=docs_O&mode=details&doc_id={$order.doc_id}">{$order.date|date_format:$config.Appearance.datetime_format}</a></td>
    <td nowrap="nowrap" class="text-right">
	<a href="index.php?target=docs_O&mode=details&doc_id={$order.doc_id}">{include file='common/currency.tpl' value=$order.total}</a>
	</td>
	<td nowrap="nowrap" class="text-right">
	<a href="index.php?target=docs_O&mode=details&doc_id={$order.doc_id}">{include file='common/currency.tpl' value=$order.tax}</a>
	</td>
	<td nowrap="nowrap" class="text-right">
		<a href="index.php?target=docs_O&mode=details&doc_id={$order.doc_id}">{include file='common/currency.tpl' value=$order.extra.shipping_no_offer} ({include file='common/currency.tpl' value=$order.shipping_cost})</a>
	</td>
	<td nowrap="nowrap" class="text-right">
		<a href="index.php?target=docs_O&mode=details&doc_id={$order.doc_id}">{include file='common/currency.tpl' value=$order.cost}</a>
	</td>
	<td nowrap="nowrap" class="text-right">
		<a href="index.php?target=docs_O&mode=details&doc_id={$order.doc_id}">{$order.items}</a>
	</td>
	<td nowrap="nowrap" class="text-right">
		<a href="index.php?target=docs_O&mode=details&doc_id={$order.doc_id}">{include file='common/currency.tpl' value=$order.profit display_sign=true}</a>
	</td>
</tr>

{/foreach}

{assign var='total_items' value=$smarty.foreach.items.total}
{math equation="x / y" x=$s_total y=$total_items assign="a_total"}
{math equation="x / y" x=$s_tax y=$total_items assign="a_tax"}
{math equation="x / y" x=$s_shipping_cost y=$total_items assign="a_shipping_cost"}
{math equation="x / y" x=$s_shipping_cost_no_offer y=$total_items assign="a_shipping_cost_no_offer"}
{math equation="x / y" x=$s_cost y=$total_items assign="a_cost"}
{math equation="x / y" x=$s_items y=$total_items assign="a_items"}
{math equation="x / y" x=$s_profit y=$total_items assign="a_profit"}

<tr class='cycle'>
	<td colspan="5"></td>
	<td class="text-right"><strong>{$lng.lbl_total}</strong></td>
	<td class="text-right"><strong>{$lng.lbl_tax}</strong></td>
	<td class="text-right"><strong>{$lng.lbl_shipping}</strong></td>
	<td class="text-right"><strong>{$lng.lbl_cost}</strong></td>
	<td class="text-right"><strong>{$lng.lbl_items}</strong></td>
	<td class="text-right"><strong>{$lng.lbl_order_profit}</strong></td>
</tr>
<tr>
	<td colspan="5"><strong>{$lng.lbl_totals}</strong></td>
	<td class="text-right"><strong>{include file='common/currency.tpl' value=$s_total}</strong></td>
	<td class="text-right"><strong>{include file='common/currency.tpl' value=$s_tax}</strong></td>
	<td class="text-right"><strong>{include file='common/currency.tpl' value=$s_shipping_cost_no_offer}&nbsp;({include file='common/currency.tpl' value=$s_shipping_cost})</strong></td>
	<td class="text-right"><strong>{include file='common/currency.tpl' value=$s_cost}</strong></td>
	<td class="text-right"><strong>{$s_items|formatprice}</strong></td>
	<td class="text-right"><strong>{include file='common/currency.tpl' value=$s_profit display_sign=true}</strong></td>
</tr>
<tr>
	<td colspan="5"><strong>{$lng.lbl_averages}</strong></td>
	<td class="text-right"><strong>{include file='common/currency.tpl' value=$a_total}</strong></td>
	<td class="text-right"><strong>{include file='common/currency.tpl' value=$a_tax}</strong></td>
	<td class="text-right"><strong>{include file='common/currency.tpl' value=$a_shipping_cost_no_offer} ({include file='common/currency.tpl' value=$a_shipping_cost})</strong></td>
	<td class="text-right"><strong>{include file='common/currency.tpl' value=$a_cost}</strong></td>
	<td class="text-right"><strong>{$a_items|formatprice}</strong></td>
	<td class="text-right"><strong>{include file='common/currency.tpl' value=$a_profit display_sign=true}</strong></td>
</tr>
</tbody>
</table>

<script language="javascript">
{literal}
$(document).ready(function() {
	$('.order_tooltip').each(function() {
		$(this).tooltip({
			'onCreate': function(ele, options) {
				options.openTrigger = 'hover';
				options.closeTrigger = 'hover';
				options.content = $(ele).attr("title").replace(/\n/g,"<br>");
				$(ele).attr("title", null);
			}
		});
	});
});
{/literal}
</script>
</div>

</form>
{/if}
