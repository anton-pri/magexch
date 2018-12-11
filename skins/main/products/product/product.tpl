{capture name=section}
<table width="100%">
<tr>
	<td valign="top" align="left" width="30%">
{include file='common/thumbnail.tpl' image=$product.image}
<p />
{if $smarty.get.mode ne "printable"}
<a href="index.php?target=products&mode=details&product_id={$product.product_id}&amp;mode=printable" target="_blank"><img src="{$ImagesDir}/print.gif" width="23" height="22" name="print" alt="{$lng.lbl_printable_version|escape}" /></a>
{/if}
	</td>
	<td valign="top">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td>{$lng.lbl_sku}</td>
	<td>{$product.productcode}</td>
</tr>
<tr>
	<td>{$lng.lbl_category}</td>
	<td>{$product.category_text}</td>
</tr>
{if $usertype eq "A"}
<tr>
	<td>{$lng.lbl_vendor}</td>
	<td>{$product.warehouse}</td>
</tr>
{/if}
<tr>
	<td colspan="2">
<br />
<br />
<span class="Text">{$product.descr}</span>
<br />
<br />
	</td>
</tr>
<tr>
	<td colspan="2"><b><font class="ProductDetailsTitle">{$lng.lbl_price_info}</font></b></td>
</tr>
<tr>
	<td class="Line" height="1" colspan="2"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
<tr>
	<td width="50%">{$lng.lbl_price}</td>
	<td nowrap="nowrap"><font class="ProductPriceSmall">{include file='common/currency.tpl' value=$product.price}</font></td>
</tr>
<tr>
	<td width="50%">{$lng.lbl_in_stock}</td>
	<td nowrap="nowrap">{$lng.lbl_items_available|substitute:"items":$product.avail}</td>
</tr>
<tr>
	<td width="50%">{$lng.lbl_weight}</td>
	<td nowrap="nowrap">{$product.weight} {$config.General.weight_symbol}</td>
</tr>
</table>
<br />

<table cellspacing="0" cellpadding="0">
<tr>
	<td>{include file="buttons/modify.tpl" href="index.php?target=products&mode=details&product_id=`$product.product_id`"}</td>
	<td>&nbsp;&nbsp;</td>
	<td>{include file="buttons/clone.tpl" href="index.php?target=process_product&mode=clone&product_id=`$product.product_id`"}</td>
	<td>&nbsp;&nbsp;</td>
	<td>{include file="buttons/delete.tpl" href="index.php?target=process_product&mode=delete&product_id=`$product.product_id`"}</td>
</tr>
</table>

	</td>
</tr>
</table>
{/capture}
{if $smarty.get.mode eq "printable"}
{include file="common/section.tpl" title=$product.producttitle content=$smarty.capture.section extra="width=440"}
{else}
{include file="common/section.tpl" title=$product.producttitle content=$smarty.capture.section extra='width="100%"'}
{/if}
