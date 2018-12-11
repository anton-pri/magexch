{if $products}

{assign var='s_cost' value=0.00}
{assign var='s_avg_price' value=0.00}
{assign var='s_avg_profit' value=0.00}
{assign var='s_qty' value=0}
{assign var='s_total_cost' value=0.00}
{assign var='s_total_sales' value=0.00}
{assign var='s_total_profit' value=0.00}
{assign var='s_margin' value=0.00}
{assign var='s_markup' value=0.00}

{assign var='a_cost' value=0.00}
{assign var='a_avg_price' value=0.00}
{assign var='a_avg_profit' value=0.00}
{assign var='a_qty' value=0}
{assign var='a_margin' value=0.00}
{assign var='a_markup' value=0.00}

<div class="box orders">

<input type="hidden" name="action" value="" />

<table class="table table-striped dataTable" width="100%">
<thead>
<tr>
    <th width="5%">{if $search_prefilled.sort_field eq "product_id"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=product_id&amp;sort_direction={if $search_prefilled.sort_field eq 'product_id'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">#</a></th>
    <th>{if $search_prefilled.sort_field eq "product"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=product&amp;sort_direction={if $search_prefilled.sort_field eq 'product'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_product}</a></th>
    <th>{if $search_prefilled.sort_field eq "SupplierName"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=SupplierName&amp;sort_direction={if $search_prefilled.sort_field eq 'SupplierName'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">Supplier</a></th>
    <th class="text-right">{if $search_prefilled.sort_field eq "cost"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=cost&amp;sort_direction={if $search_prefilled.sort_field eq 'cost'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_cost}</a></th>
    <th class="text-right">{if $search_prefilled.sort_field eq "avg_price"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=avg_price&amp;sort_direction={if $search_prefilled.sort_field eq 'avg_price'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_avg_price}</a></th>
    <th class="text-right">{if $search_prefilled.sort_field eq "avg_profit"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=avg_profit&amp;sort_direction={if $search_prefilled.sort_field eq 'avg_profit'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_avg_profit}</a></th>
    <th class="text-right">{if $search_prefilled.sort_field eq "qty"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=qty&amp;sort_direction={if $search_prefilled.sort_field eq 'qty'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_qty}</a></th>
    <th class="text-right">{if $search_prefilled.sort_field eq "total_cost"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=total_cost&amp;sort_direction={if $search_prefilled.sort_field eq 'total_cost'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_total_cost}</a></th>
    <th class="text-right">{if $search_prefilled.sort_field eq "total_sales"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=total_sales&amp;sort_direction={if $search_prefilled.sort_field eq 'total_sales'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_total_sales}</a></th>
    <th class="text-right">{if $search_prefilled.sort_field eq "total_profit"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=total_profit&amp;sort_direction={if $search_prefilled.sort_field eq 'total_profit'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_over_total_profit}</a></th>
    <th class="text-right">{if $search_prefilled.sort_field eq "margin"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=margin&amp;sort_direction={if $search_prefilled.sort_field eq 'margin'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_margin}</a></th>
    <th class="text-right">{if $search_prefilled.sort_field eq "markup"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=markup&amp;sort_direction={if $search_prefilled.sort_field eq 'markup'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_markup}</a></th>
</tr>
</thead>
<tbody>
{assign var='href' value='index.php?target=products&mode=details&product_id='}
{foreach from=$products item=product name=items}

{math equation="x + y" x=$s_cost y=$product.cost assign="s_cost"}
{math equation="x + y" x=$s_avg_price y=$product.avg_price assign="s_avg_price"}
{math equation="x + y" x=$s_avg_profit y=$product.avg_profit assign="s_avg_profit"}
{math equation="x + y" x=$s_qty y=$product.qty assign="s_qty"}
{math equation="x + y" x=$s_total_cost y=$product.total_cost assign="s_total_cost"}
{math equation="x + y" x=$s_total_sales y=$product.total_sales assign="s_total_sales"}
{math equation="x + y" x=$s_total_profit y=$product.total_profit assign="s_total_profit"}
{math equation="x + y" x=$s_margin y=$product.margin assign="s_margin"}
{math equation="x + y" x=$s_markup y=$product.markup assign="s_markup"}
<tr {cycle values=", class='cycle'"}>
	<td nowrap="nowrap"><!-- cw@profit_reports_product_column --><a href="{$href}{$product.product_id}">#{$product.product_id}</a></td>
	<td class="font-w600"><a href="{$href}{$product.product_id}">{$product.product}</a></td>
    <td align="right">{$product.SupplierName}</td>
	<td class="text-right">{include file='common/currency.tpl' value=$product.cost}</td>
	<td class="text-right">{include file='common/currency.tpl' value=$product.avg_price}</td>
	<td class="text-right">{include file='common/currency.tpl' value=$product.avg_profit}</td>
	<td class="text-right">{$product.qty}</td>
	<td class="text-right">{include file='common/currency.tpl' value=$product.total_cost}</td>
	<td class="text-right">{include file='common/currency.tpl' value=$product.total_sales}</td>
	<td class="text-right">{include file='common/currency.tpl' value=$product.total_profit}</td>
	<td class="text-right">{$product.margin|formatprice}</td>
	<td class="text-right">{$product.markup|formatprice}</td>
</tr>

{/foreach}

{assign var='total_items' value=$smarty.foreach.items.total}
{math equation="x / y" x=$s_cost y=$total_items assign="a_cost"}
{math equation="x / y" x=$s_avg_price y=$total_items assign="a_avg_price"}
{math equation="x / y" x=$s_avg_profit y=$total_items assign="a_avg_profit"}
{math equation="x / y" x=$s_qty y=$total_items assign="a_qty"}
{math equation="x / y" x=$s_margin y=$total_items assign="a_margin"}
{math equation="x / y" x=$s_markup y=$total_items assign="a_markup"}

<tr class='cycle'>
	<td colspan="3"></td>
	<td class="text-right"><strong>{$lng.lbl_cost}</strong></td>
	<td class="text-right"><strong>{$lng.lbl_avg_price}</strong></td>
	<td class="text-right"><strong>{$lng.lbl_avg_profit}</strong></td>
	<td class="text-right"><strong>{$lng.lbl_qty}</strong></td>
	<td class="text-right"><strong>{$lng.lbl_total_cost}</strong></td>
	<td class="text-right"><strong>{$lng.lbl_total_sales}</strong></td>
	<td class="text-right"><strong>{$lng.lbl_over_total_profit}</strong></td>
	<td class="text-right"><strong>{$lng.lbl_margin}</strong></td>
	<td class="text-right"><strong>{$lng.lbl_markup}</strong></td>
</tr>
<tr>
	<td colspan="3"><strong>{$lng.lbl_totals}</strong></td>
	<td></td>
	<td></td>
	<td></td>
	<td class="text-right"><strong>{$s_qty}</strong></td>
	<td class="text-right"><strong>{include file='common/currency.tpl' value=$s_total_cost}</strong></td>
	<td class="text-right"><strong>{include file='common/currency.tpl' value=$s_total_sales}</strong></td>
	<td class="text-right"><strong>{include file='common/currency.tpl' value=$s_total_profit}</strong></td>
	<td class="text-right"></td>
	<td class="text-right"></td>
</tr>
<tr>
	<td colspan="3"><strong>{$lng.lbl_averages}</strong></td>
	<td class="text-right"><strong>{include file='common/currency.tpl' value=$a_cost}</strong></td>
	<td class="text-right"><strong>{include file='common/currency.tpl' value=$a_avg_price}</strong></td>
	<td class="text-right"><strong>{include file='common/currency.tpl' value=$a_avg_profit}</strong></td>
	<td class="text-right"><strong>{$a_qty|formatprice}</strong></td>
	<td class="text-right"></td>
	<td class="text-right"></td>
	<td class="text-right"></td>
	<td class="text-right"><strong>{$a_margin|formatprice}</strong></td>
	<td class="text-right"><strong>{$a_markup|formatprice}</strong></td>
</tr>
</tbody>

</table>

</div>

{/if}
