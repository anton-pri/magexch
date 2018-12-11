{include file='common/page_title.tpl' title=$lng.lbl_wish_list}

{$lng.txt_admin_wishlists}

<br /><br />

{capture name=section}

<div align="right">{include file='buttons/button.tpl' button_title=$lng.lbl_search_again href="index.php?target=wishlists"}</div>

<b>{$lng.lbl_customer}:</b>

{$wishlist.0.firstname} {$wishlist.0.lastname} ({$wishlist.0.customer_id})

<br /><br />

<table cellpadding="1" cellspacing="2" width="100%">
<tr class="TableHead">
	<td width="10%" nowrap="nowrap">{$lng.lbl_sku}</td>
	<td width="45%" nowrap="nowrap">{$lng.lbl_product}</td>
	<td width="35%" nowrap="nowrap">{$lng.lbl_selected_options}</td>
	<td width="10%" nowrap="nowrap">{$lng.lbl_quantity}</td>
</tr>

{foreach from=$wishlist item=v}
<tr{cycle name=c1 values=', class="TableSubHead"'}>
	<td>{if $acc and $accl.1004}<a href="index.php?target=products&mode=details&product_id={$v.product_id}">{/if}{$v.productcode}{if $acc and $accl.1004}</a>{/if}</td>
	<td>{if $acc and $accl.1004}<a href="index.php?target=products&mode=details&product_id={$v.product_id}">{/if}{$v.product|truncate:35:"...":false}{if $acc and $accl.1004}</a>{/if}</td>
{* kornev, TOFIX *}
	<td>{if $v.product_options ne ''}{include file="addons/product_options/main/options/display.tpl" options=$v.product_options}{/if}</td>
	<td align="center">{$v.amount}</td>
</tr>
{/foreach}

</table>
<br />

{/capture}
{include file="common/section.tpl" title=$lng.lbl_wish_list content=$smarty.capture.section extra='width="100%"'}
