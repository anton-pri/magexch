<table class="table table-striped table-borderless table-header-bg" width="100%">
<thead>
  <tr{cycle values=", class='cycle'"}>
    <th width="1%">{$lng.lbl_del}</th>
    <th width="10%">{$lng.lbl_eancode}</th>
{if $doc.type eq 'P' or $doc.type eq 'Q' or $doc.type eq 'R' or $doc.type eq 'D'}
    <th width="10%">{$lng.lbl_supplier_sku}</th>
{/if}
{if $doc.type eq 'P' or $doc.type eq 'Q' or $doc.type eq 'R'}
    <th width="10%">{$lng.lbl_is_auto_calc}/{$lng.lbl_end_price}</th>
{elseif $doc.type eq 'D'}
    <th width="1%">{$lng.lbl_is_auto_calc}</th>
{/if}
    <th>{$lng.lbl_product}</th>
{*    
    <th width="10%">{$lng.lbl_net_price}</th>
    <th width="10%">{$lng.lbl_net_total}</th>
*}
    
{if $doc.type ne 'D'}
    <th width="10%">{$lng.lbl_price}</th>
    <th width="10%">{$lng.lbl_item_price}</th>
    <th width="10%">{$lng.lbl_discount} (%)</th>
{/if}
    <th width="10%">{$lng.lbl_in_stock}</th>
    <th width="10%">{$lng.lbl_quantity}</th>
  </tr>
</thead>

{if $products}
{foreach from=$products item=product key=index}
<tr{cycle values=", class='cycle'"}>
    <td>
        <input type="checkbox" name="product_details[{$index}][delete]" value="1" onclick="javascript: cw_doc_delete_item('{$doc_id}', '{$index}');" />
    </td>
    <td align="center">{$product.eancode}</td>
{if $doc.type eq 'P' or $doc.type eq 'Q' or $doc.type eq 'R' or $doc.type eq 'D'}
    <td align="center"><input type="text" name="product_details[{$index}][productcode]" value="{$product.productcode|escape}" size="10" onchange="javascript: cw_doc_update_item_info('{$doc_id}', '{$index}', 'edit_products_form')" /></td>
{/if}
    <td>
        {if $current_area eq 'G'}
        {$product.product}
        {elseif $current_area eq 'C'}
        <a href="{pages_url var="product" product_id=$product.product_id}" target="viewproduct{$product.product_id}">{$product.product}</a>
        {else}
        <a href="index.php?target=products&mode=details&product_id={$product.product_id}" target="viewproduct{$product.product_id}">{$product.product}</a><br/>
        {/if}

{* kornev, TOFIX *}
{if $product.display_options}
<script type="text/javascript">
<!--
var avail = '{$product.avail|default:1}';
var min_avail = '{$product.min_avail|default:1}';
-->
</script>
<table cellpadding="1" cellspacing="1">
{include file='addons/product_options/customer/products/product-amount.tpl' product_options=$product.display_options nojs='Y' cname="product_details[`$index`][product_options]" onchange="javascript: cw_doc_update_item_info('`$doc_id`', '`$index`', 'edit_products_form')"}
</table>
{/if}
    </td>
    {*
    <td align="center" nowrap>{include file='common/currency.tpl' value=$product.display_net_price|default:0}</td>
    <td align="center" nowrap>{include file='common/currency.tpl' value=$product.display_net_price*$product.amount}</td>
    *}
{if $doc.type ne 'D'}
    <td align="center">
{if ($addons.egoods && $product.distribution ne '') || $current_area eq 'C'}
{include file='common/currency.tpl' value=$product.display_price}
{else}
<input type="text" name="product_details[{$index}][price]" size="10" maxlength="15" value="{$product.price|formatprice}" onchange="javascript: cw_doc_update_item_info('{$doc_id}', '{$index}', 'edit_products_form')"{if $current_area eq 'G' and !$accl.100001} disabled{/if}/>
{/if}
    </td>
    <td align="center">{include file='common/currency.tpl' value=$product.display_price}</td>
    <td nowrap align="center">
    {if $product.discount_avail}
{*
        {math equation="(1+new_price/price)*100" new_price=$product.price price=$product.net_price|default:$product.price assign="discount"}
        {assign var='discount' value=$discount|formatprice}
*}
    {else}
{if $product.price >= $product.net_price}
{assign var='discount' value=0}
{elseif $product.discount_formula && $product.discount_avail}
{assign var='discount' value=$product.discount_formula}
{else}
    {if $product.price && $product.net_price && $product.discount_avail}
{math equation="(1-new_price/price)*100" new_price=$product.price price=$product.net_price|default:$product.price assign="discount"}
{assign var='discount' value=$discount|formatprice}
    {else}
{assign var='discount' value=0}
    {/if}
{/if}
    {/if}
{if $current_area eq 'C'}
    {$discount}
{else}
<input type="checkbox" class="table_check" name="product_details[{$index}][use_discount]" onclick="javascript: $('#product_details_{$index}_discount').prop('disabled',!this.checked?'disabled':'');" value="1"{if ($current_area eq 'G' and !$accl.100000) || !$product.discount_avail} disabled{/if}{if $discount} checked{/if}>
<input type="text" id="product_details_{$index}_discount" name="product_details[{$index}][discount]" size="6" value="{$discount}"{if !$discount} disabled{/if} onchange="javascript: cw_doc_update_item_info('{$doc_id}', '{$index}', 'edit_products_form')"/>
{/if}
    </td>
{/if}
    <td align="center">{$product.items_in_stock}/{$product.items_in_backorder}</td>
    <td align="center">
        <input type="text" name="product_details[{$index}][amount]" size="5" maxlength="5" value="{$product.amount}" onchange="javascript: cw_doc_update_item_info('{$doc_id}', '{$index}', 'edit_products_form')" />
    </td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="9" align="center">{$lng.lbl_aom_no_products_ordered}</td>
</tr>
{/if}
</table>
