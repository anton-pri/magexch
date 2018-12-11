{if $products}
<input type="hidden" name="sort" value="{$search_prefilled.sort_field}" />
<input type="hidden" name="sort_direction" value="{$search_prefilled.sort_direction}" />

<table class="table table-striped dataTable vertical-center" width="100%">

{if $main eq "category_products"}
{assign var="url_to" value=$navigation.script}
{else}
{assign var="url_to" value="index.php?target=products&mode=search&page=`$navpage`"}
{/if}

<tr>
	<th width="10"><input type='checkbox' class='select_all' class_to_select='products_item' /></th>
	<th width='125'>{if $search_prefilled.sort_field eq "productcode"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$url_to|amp}&amp;sort=productcode&amp;sort_direction={if $search_prefilled.sort_field eq 'productcode'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_sku}</a></th>
	<th width="50%">{if $search_prefilled.sort_field eq "title"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$url_to|amp}&amp;sort=title&amp;sort_direction={if $search_prefilled.sort_field eq 'title'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_product}</a></th>
{if $main eq "category_products"}
	<th>{if $search_prefilled.sort_field eq 'orderby'}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$url_to|amp}&amp;sort=orderby&amp;sort_direction={if $search_prefilled.sort_field eq 'orderby'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_pos}</a></th>
{/if}
	<th class="text-center">{if $search_prefilled.sort_field eq 'quantity'}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$url_to|amp}&amp;sort=quantity&amp;sort_direction={if $search_prefilled.sort_field eq 'quantity'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_in_stock}</a></th>
	<th class="text-center">{if $search_prefilled.sort_field eq 'price'}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$url_to|amp}&amp;sort=price&amp;sort_direction={if $search_prefilled.sort_field eq 'price'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_price} ({$config.General.currency_symbol})</a></th>
{if $usertype eq 'A'}
    <th>{$lng.lbl_availability}</th>
{/if}
</tr>

{foreach from=$products item=product}
{assign var='pid' value=$product.product_id}
<tr{cycle values=', class="cycle"'}>
	<td><input type="checkbox" name="product_ids[{$product.product_id}]" class="products_item" /></td>
	<td><span {*edit_on_place table="products" pk=$product.product_id field="productcode"*}><a href="index.php?target={$current_target}&mode=details&product_id={$product.product_id}{if $navpage}&page={$navpage}{/if}">{$product.productcode}</a></span></td>
	<td>{if $product.main eq "Y" or $main ne "category_products"}<b>{/if}<span {*edit_on_place table="products" pk=$product.product_id field="product" handler="cw_edit_on_place_product"*}><a href="index.php?target={$current_target}&mode=details&product_id={$product.product_id}{if $navpage}&page={$navpage}{/if}">{$product.product}</a></span>{if $product.main eq "Y" or $main ne "category_products"}</b>{/if}</td>
{if $main eq "category_products"}
	<td><input type="text" size="9" maxlength="10" name="posted_data[{$product.product_id}][orderby]" value="{$products_orderbys.$pid.orderby|default:0}"  class="form-control" style="width:60px"/></td>
{/if}
	<td nowrap align="center">
{if $product.is_variants ne 'Y'}
<input type="text" class="form-control" size="9" maxlength="10" name="posted_data[{$product.product_id}][avail]" value="{$product.avail|default:0}" class="input50" token='{*edit_on_place table="products_warehouses_amount" where="product_id='`$product.product_id`' AND variant_id=0" field="avail" token_only=true*}' />
{if $addons.warehouse && $usertype eq "A" && $product.avails}
{include file='main/visiblebox_link.tpl' mark="open_close_product_avails_`$product.product_id`"}
{/if}
{/if}
	</td>
	<td nowrap align="center">
{*if $product.is_variants ne 'Y'}
<input type="checkbox" name="posted_data[{$product.product_id}][is_manual_price]" value="1"{if $product.is_manual_price} checked{/if}{if $read_only} disabled{else} onchange="javascript: document.getElementById('product_data_{$product.product_id}_price').disabled = !this.checked"{/if} />
<input type="text" class="form-control" size="9" maxlength="15" name="posted_data[{$product.product_id}][price]" id="product_data_{$product.product_id}_price" value="{$product.price|formatprice}"{if $usertype ne 'A' || !$product.is_manual_price} disabled{/if}/>
{if $addons.warehouse && $usertype eq "A" && $product.prices}
{include file='main/visiblebox_link.tpl' mark="open_close_product_prices_`$product.product_id`"}
{/if}
{/if*}
        {include file='common/currency.tpl' value=$product.price}
	</td>
{if $usertype eq 'A'}
    <td class="avail">
        <input type="hidden" name="posted_data[{$product.product_id}][status]" value="0" />
        {include file='admin/select/availability_product.tpl' name="posted_data[`$product.product_id`][status]" value=$product.status}
    </td>
{/if}
</tr>
{if $addons.warehouse && $usertype eq "A" && $product.avails}
<tr style="display:none;" id="open_close_product_avails_{$product.product_id}">
    <td>&nbsp;</td>
    <td colspan="4">
    {include file='main/products/product/avails.tpl' avails_summ=$product.avails simple=true}
    </td>
</tr>
{/if}
{if $addons.warehouse && $usertype eq "A" && $product.prices}
<tr style="display:none;" id="open_close_product_prices_{$product.product_id}">
    <td>&nbsp;</td>
    <td colspan="4">
    {include file='main/products/product/prices.tpl' prices=$product.prices simple=true}
    </td>
</tr>
{/if}  
{/foreach}

</table>
{/if}
