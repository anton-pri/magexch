<div class="dialog_title">{$lng.txt_reffered_sales_note}</div>

{capture name=section}
<form action="index.php?target=referred_sales" method="post" name="referredsalesform">

<div class="input_field_0">
	<label>{$lng.lbl_date}</label>
    {include file='main/select/date.tpl' name='posted_data[start_date]' value=$search.start_date} -
    {include file='main/select/date.tpl' name='posted_data[end_date]' value=$search.end_date}
</div>
<div class="input_field_0">
    <label>{$lng.lbl_sku}</label>
    <input type="text" name="search[productcode]" size="20" value="{$search.productcode}" />
</div>
<div class="input_field_0">
    <label>{$lng.lbl_show_top_products}</label>
    <input type="checkbox" name="search[top]" value="Y"{if $search.top eq 'Y'} checked="checked"{/if} />
</div>
{if $usertype eq 'A' || ($usertype eq 'P' && $addons.Simple_Mode ne '')}
<div class="input_field_0">
    <label>{$lng.lbl_salesman}:</label>
    {include file='main/select/salesman.tpl' name='search[salesman]' value=$search.salesman}
</div>
{/if}
<div class="input_field_0">
    <label>{$lng.lbl_status}</label>
	<select name="search[status]">
		<option value=''{if $search.status eq ''} selected="selected"{/if}>{$lng.lbl_all}</option>
	    <option value='N'{if $search.status eq 'N'} selected="selected"{/if}>{$lng.lbl_pending}</option>
	    <option value='Y'{if $search.status eq 'Y'} selected="selected"{/if}>{$lng.lbl_paid}</option>
	</select>
</div>
<input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" />
</form>
{/capture}
{include file='common/section.tpl' content=$smarty.capture.section title=$lng.lbl_search}

{if $sales ne '' && $usertype ne 'B'}

{capture name=section}
<table class="header" width="100%">
<tr>
{if ($usertype eq 'A' || ($usertype eq 'P' && $addons.Simple_Mode ne '')) && $search.top ne 'Y'}
    <th>{$lng.lbl_salesman}</th>
    <th>{$lng.lbl_salesman_parent}</th>
{/if}
	<th>{$lng.lbl_product}</th>
{if $search.top ne 'Y'}
	<th>{$lng.lbl_order}</th>
{/if}
	<th>{$lng.lbl_quantity}</th>
{if $config.Salesman.salesman_allow_see_total eq 'Y' || $usertype ne 'B'}
	<th>{$lng.lbl_total}</th>
{/if}
    <th>{$lng.lbl_commission}</th>
{if $search.top ne 'Y'}
	<th>{$lng.lbl_status}</th>
{/if}
</tr>
{assign var="total_amount" value=0}
{assign var="total_total" value=0}
{assign var="total_product_commissions" value=0}
{foreach from=$sales item=v}
<tr>
{if ($usertype eq 'A' || ($usertype eq 'P' && $addons.Simple_Mode ne '')) && $search.top ne 'Y'}
	<td><a href="index.php?target=user_modify&user={$v.customer_id|escape:"url"}&amp;usertype=B">{$v.email}</a></td>
	<td>{if $v.parent_customer_id ne ''}<a href="index.php?target=user_modify&user={$v.parent_customer_id|escape:"url"}&amp;usertype=B">{$v.parent_customer_id}</a>{/if}</td>
{/if}
	<td>{if $usertype eq 'A' || ($usertype eq 'P' && $addons.Simple_Mode ne '')}<a href="index.php?target=products&mode=details&product_id={$v.product_id}">{/if}{$v.product}{if $usertype eq 'A' || ($usertype eq 'P' && $addons.Simple_Mode ne '')}</a>{/if}</td>
{if $search.top ne 'Y'}
    <td>{if $usertype eq 'A' || ($usertype eq 'P' && $addons.Simple_Mode ne '')}<a href="index.php?target=order&doc_id={$v.doc_id}">{/if}{$v.display_id}{if $usertype eq 'A' || ($usertype eq 'P' && $addons.Simple_Mode ne '')}</a>{/if}</td>
	<td>{$v.add_date|date_format:$config.Appearance.date_format}</td>
{/if}
	<td>{$v.amount}</td>
{math assign="total_amount" equation="x+y" x=$total_amount y=$v.amount}
{if $config.Salesman.salesman_allow_see_total eq 'Y' || $usertype ne 'B'}
	<td>{include file='common/currency.tpl' value=$v.total}</td>
{math assign="total_total" equation="x+y" x=$total_total y=$v.total}
{/if}
	<td>{include file='common/currency.tpl' value=$v.product_commission}</td>
{math assign="total_product_commissions" equation="x+y" x=$total_product_commissions y=$v.product_commission}
{if $search.top ne 'Y'}
	<td>{if $v.paid eq 'Y'}{$lng.lbl_paid}{else}{$lng.lbl_pending}{/if}</td>
{/if}
</tr>
{/foreach}
{assign var="colspan_count" value=3}
{if ($usertype eq 'A' || ($usertype eq 'P' && $addons.Simple_Mode ne '')) && $search.top ne 'Y'}{math assign="colspan_count" equation="x+2" x=$colspan_count}{/if}
{if $search.top ne 'Y'}{math assign="colspan_count" equation="x+2" x=$colspan_count}{/if}
{if $usertype eq 'B' && $search.top ne 'Y'}
<tr>
	<td colspan="{$colspan_count}">{$lng.lbl_pending_aff_commissions}</td>
	<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$parent_pending}</td>
</tr>
{math assign="total_product_commissions" equation="x+y" x=$total_product_commissions y=$parent_pending|default:0}
<tr>
    <td colspan="{$colspan_count}">{$lng.lbl_paid_aff_commissions}</td>
	<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$parent_paid}</td>
{math assign="total_product_commissions" equation="x+y" x=$total_product_commissions y=$parent_paid|default:0}
</tr>
{/if}
{if $search.top ne 'Y'}{math assign="colspan_count" equation="x+1" x=$colspan_count}{/if}

<tr>
{assign var="colspan_count" value=1}
{if ($usertype eq 'A' || ($usertype eq 'P' && $addons.Simple_Mode ne '')) && $search.top ne 'Y'}{math assign="colspan_count" equation="x+2" x=$colspan_count}{/if}
{if $search.top ne 'Y'}{math assign="colspan_count" equation="x+2" x=$colspan_count}{/if}
<td colspan="{$colspan_count}"><b>{$lng.lbl_total}:</b></td>
	<td>{$total_amount}</td>
{if $config.Salesman.salesman_allow_see_total eq 'Y' || $usertype ne 'B'}
	<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$total_total|default:"0"}</td>
{/if}
	<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$total_product_commissions|default:"0"}</td>
</tr>
</table>

{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_sales extra='width="100%"'}

{elseif $sales ne '' and $usertype eq 'B'}

{capture name=section}
<table class="header" width="100%">
<tr>
    <th>{$lng.lbl_order}</th>
    <th>{$lng.lbl_date}</th>
    <th>{$lng.lbl_customer}</th>
    <th>{$lng.lbl_quantity}</th>
{if $config.Salesman.salesman_allow_see_total eq 'Y'}
    <th>{$lng.lbl_total}</th>
{/if}
    <th>{$lng.lbl_commission}</th>
    <th>{$lng.lbl_status}</th>
    <th>&nbsp;</th>
</tr>
{assign var="total_amount" value=0}
{assign var="total_total" value=0}
{assign var="total_product_commissions" value=0}
{foreach from=$sales item=v}
<div class="input_field_0">
    <label>{$v.order.display_id}</label>
    <label nowrap="nowrap">{$v.order.date|date_format:$config.Appearance.date_format}</label>
    <label>{if $v.order.usertype eq 'R'}{$v.order.company}{else}{$v.order.firstname} {$v.order.lastname}{/if}</label>
    <label>{$v.amount}</label>
{math assign="total_amount" equation="x+y" x=$total_amount y=$v.amount}
{if $config.Salesman.salesman_allow_see_total eq 'Y'}
    <label align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$v.total}</label>
{math assign="total_total" equation="x+y" x=$total_total y=$v.total}
{/if}
    <label align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$v.product_commission}</label>
{math assign="total_product_commissions" equation="x+y" x=$total_product_commissions y=$v.product_commission}
    <label align="right">{if $v.paid eq 'Y'}{$lng.lbl_paid}{else}{$lng.lbl_pending}{/if}</label>
    <label>
<img src="{$ImagesDir}/plus.gif" onclick="javascript:switch_elm_visibility(this, 'open_close_products{$v.order.doc_id}')" border="0" >
    </label>
</div>
<tbody id="open_close_products{$v.order.doc_id}" style="display:none;">
{foreach from=$v.products item=product name="ref_products"}
<tr>
    <td colspan="3">{$product.product}</td>
    <td>{$product.amount}</td>
{if $config.Salesman.salesman_allow_see_total eq 'Y'}
    <td align="right">{$product.total}</td>
{/if}
    <td align="right" class="ReferedSales{if $smarty.foreach.ref_products.last}Border{/if}">{$product.product_commission}</td>
    <td colspan="2">&nbsp;</td>
</tr>
{/foreach}
</tbody>
{/foreach}
<tr>
    <td colspan="5">{$lng.lbl_pending_aff_commissions}</td>
    <td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$parent_pending}</td>
</tr>
{math assign="total_product_commissions" equation="x+y" x=$total_product_commissions y=$parent_pending|default:0}
{if $config.Salesman.salesman_allow_see_total eq 'Y'}
<tr>
    <td colspan="5">{$lng.lbl_paid_aff_commissions}</td>
    <td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$parent_paid}</td>
{math assign="total_product_commissions" equation="x+y" x=$total_product_commissions y=$parent_paid|default:0}
</tr>
{/if}

<tr>
{assign var="colspan_count" value=1}
    <td colspan="3"><b>{$lng.lbl_total}:</b></td>
    <td>{$total_amount}</td>
{if $config.Salesman.salesman_allow_see_total eq 'Y'}
    <td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$total_total|default:"0"}</td>
{/if}
    <td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$total_product_commissions|default:"0"}</td>
</tr>
</table>

{/capture}
{include file='common/section.tpl' content=$smarty.capture.section title=$lng.lbl_sales}

{/if}
