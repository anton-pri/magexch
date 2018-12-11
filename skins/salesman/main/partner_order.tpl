{capture name=section}
<table cellspacing="1" cellpadding="2">

{foreach from=$order.products item=product}
<tr>
    <td colspan="3" height="18" class="ProductTitle">
    <a href="index.php?target=products&mode=details&product_id={$product.product_id}" target="viewproduct{$product.product_id}">#{$product.product_id}. {$product.product}</a>
    </td>
</tr>
<tr class="TableHead">
    <th height="16" align="left">&nbsp;</th>
    <th height="16" align="left">{$lng.lbl_aom_current_value}</th>
</tr>
<tr>
    <td valign="top">{$lng.lbl_sku}</td>
    <td valign="top">{$product.productcode|default:"-"}</td>
</tr>
<tr class="TableSubHead">
    <td valign="top">{$lng.lbl_warehouse}</td>
    <td valign="top">{$product.warehouse_title}</td>
</tr>
<tr>
    <td valign="top">{$lng.lbl_aom_catalog_price}</td>
    <td valign="top">{include file='common/currency.tpl' value=$product.catalog_price}</td>
</tr>
<tr class="TableSubHead">
    <td valign="top">{$lng.lbl_price}</td>
    <td valign="top">{include file='common/currency.tpl' value=$product.price}</td>
</tr>
<tr>
  <td valign="top">{$lng.lbl_aom_quantity_items}</td>
  <td valign="top">{$product.amount}</td>
</tr>
<tr class="TableSubHead">
    <td valign="top">{$lng.lbl_aom_quantity_stock_items}</td>
    <td valign="top">{$product.items_in_stock}</td>
</tr>
{* kornev, TOFIX *}
{if $product.product_options ne ""}
<tr>
    <td valign="top">{$lng.lbl_selected_options}<br />{$lng.lbl_aom_considered_in_price}</td>
    <td valign="top">{include file="addons/product_options/main/options/display.tpl" options=$product.product_options}</td>
</tr>
{/if}
<tr>
    <td valign="top" colspan="3" height="10">&nbsp;</td>
</tr>
{/foreach}
</table>

{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_salesman_orders extra='width="100%"'}
