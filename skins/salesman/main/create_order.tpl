{include_once_src file="main/include_js.tpl" src="js/popup_product.js"}

{capture name=section}
<form action="index.php?target=create_order&user={$user}" method="post" name="editpoduct_form">
<input type="hidden" name="action" value="edit" />
<input type="hidden" name="action" value="update_products" />
<input type="hidden" name="show" value="products" />

{include file="common/subheader.tpl" title=$lng.lbl_product_info}

<table cellpadding="3" cellspacing="1" width="100%">

{if $total_products}
{section name=prod_num loop=$total_products start=0}
{assign var="product" value=$cart_products[prod_num]}
{assign var="orig_product" value=$orig_products[prod_num]}
<tr>
    <td colspan="3" height="18" class="{if $product.deleted}ProductTitleHidden{else}ProductTitle{/if}">
    <a href="index.php?target=products&mode=details&product_id={$product.product_id}" target="viewproduct{$product.product_id}">
#{$product.product_id}. {$product.product}
</a>
{if $product.deleted}
    &nbsp;&nbsp;&nbsp;[<font class="ErrorMessage">{$lng.lbl_aom_deleted}</font>]
{/if}
    </td>
</tr>
<tr class="TableHead">
    <td height="16" align="left">

<table cellpadding="0" cellspacing="0">
<tr>
    <td><input type="checkbox" name="product_details[{%prod_num.index%}][delete]" value="{$product.product_id}" /></td>
    <td>{if $product.deleted}{$lng.lbl_aom_restore}{else}{$lng.lbl_aom_delete}{/if}</td>
</tr>
</table>

    </td>
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
    <td valign="top">
{if $product.deleted || ($addons.egoods && $product.distribution ne '')}
{include file='common/currency.tpl' value=$product.price}
{else}
<input type="text" name="product_details[{%prod_num.index%}][price]" size="15" maxlength="15" value="{$product.price|formatprice}" />
{/if}
    </td>
</tr>
<tr>
  <td valign="top">{$lng.lbl_aom_quantity_items}</td>
  <td valign="top">{if $product.deleted}{$product.amount}{else}<input type="text" name="product_details[{%prod_num.index%}][amount]" size="5" maxlength="5" value="{$product.amount}" />{/if}</td>
</tr>
<tr class="TableSubHead">
    <td valign="top">{$lng.lbl_aom_quantity_stock_items}</td>
    <td valign="top">{$product.items_in_stock}</td>
</tr>
{* kornev, TOFIX *}
{if $product.product_options ne ""}
<tr>
    <td valign="top">{$lng.lbl_selected_options}<br />{$lng.lbl_aom_considered_in_price}</td>
    <td valign="top">

<table cellpadding="1" cellspacing="1">
{assign var="cname" value="product_details[`$smarty.section.prod_num.index`][product_options]"}
{include file="addons/Product_Options/customer_options.tpl" product_options=$product.display_options cname=$cname disable=$product.deleted nojs='Y'}
</table>

</td>
</tr>
{/if}
<tr>
    <td valign="top" colspan="3" height="10">&nbsp;</td>
</tr>
{/section}
{else}
<tr>
    <th colspan="3">{$lng.lbl_aom_no_products_ordered}</th>
</tr>
{/if}
<tr>
    <td valign="top" colspan="3" height="10">&nbsp;</td>
</tr>
<tr>
    <td valign="top" colspan="3">{include file="common/subheader.tpl" title=$lng.lbl_add_product}</td>
</tr>
<tr>
    <td colspan="3">

<table cellpadding="0" cellspacing="0">
<tr>
	<td>
		{product_selector name_for_id='newproduct_id' name_for_name='newproduct' prefix_id='editpoduct_form' form='editpoduct_form'}
	</td>
</tr>
</table>

    </td>
</tr>

<tr>
<td colspan="3"><br />
{include file="buttons/update.tpl"  href="javascript:document.editpoduct_form.submit();"}
<br /><br />
{include file='buttons/button.tpl' button_title=$lng.lbl_send_order_to_admin href="index.php?target=create_order&user=`$user`&mode=send_to_admin"}
</td>
</tr>

</table>
</form>
{/capture}
{include file="common/section.tpl" title=$lng.lbl_create_order content=$smarty.capture.section extra='width="100%"'}
