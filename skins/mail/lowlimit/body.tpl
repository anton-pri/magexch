{include file="mail/mail_header.tpl"}
<p />{$lng.eml_lowlimit_warning_message|substitute:"sender":$config.Company.company_name:"product_id":$product.product_id}

<table cellspacing="1" cellpadding="2">
<tr>
	<td>{$lng.lbl_sku}:</td>
	<td><b>{$product.productcode}</b></td>
</tr>
<tr>
	<td>{$lng.lbl_product}:</td>
	<td><b>{$product.product}</b></td>
</tr>
{* kornev, TOFIX *}
{if $product.product_options ne ""}
<tr>
	<td>{$lng.lbl_selected_options}:</td>
	<td>{include file='addons/product_options/main/options/display.tpl' options=$product.product_options options_txt=$product.product_options_txt}</td>
</tr>
{/if}
</table>

<p />{$lng.lbl_items_in_stock|substitute:"items":$product.avail}

{include file="mail/signature.tpl"}
