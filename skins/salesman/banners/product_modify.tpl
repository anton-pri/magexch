{include file='common/page_title.tpl' title=$lng.lbl_product_html_code}
<div class="dialog_title">{$lng.txt_product_html_code_note}</div>
<br />

{capture name=section}
<table width="100%">
<tr>
	<td valign="top" align="left" rowspan="2" width="100">
{include file='common/thumbnail.tpl' image=$product.image_thumb}
	</td>
	<td valign="top">
{if $product.fulldescr ne ""}{$product.fulldescr}{else}{$product.descr}{/if}
<p />
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td colspan="2"><b><font class="ProductDetailsTitle">{$lng.lbl_details}</font></b></td>
</tr>
<tr>
	<td class="Line" height="1" colspan="2"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
{if $config.Appearance.show_in_stock eq "Y" and $product.distribution eq ""}
<tr>
	<td width="30%">{$lng.lbl_in_stock}</td>
	<td nowrap="nowrap">

    {if $product.avail gt 0}
        <table width="100%">
        <tr class="TableHead">
            <td>{$lng.lbl_warehouse}</td>
            <td>{$lng.lbl_variant}</td>
            <td>{$lng.lbl_in_stock}</td>
        </tr>
        {foreach from=$avails item=avail}
        <tr>
            <td>{$avail.warehouse_title}</td>
            <td>
            <table cellspacing="1" cellpadding="0">
            {foreach from=$avail.options item=o}
            <tr>
                <td>{$o.field}:</td>
                <td>{$o.name}</td>
            </tr>
            {/foreach}
            </table>
            </td>
            <td>{$avail.avail}</td>
        </tr>
        {/foreach}
        </table>
    {else}
{$lng.lbl_no_items_available}
    {/if}
</td>
</tr>
{/if}
<tr>
	<td width="30%">{$lng.lbl_weight}</td>
	<td nowrap="nowrap">{$product.weight} {$config.General.weight_symbol}</td>
</tr>
<tr>
	<td class="ProductPriceConverting">{$lng.lbl_price}:</td>
	<td>
{if $product.taxed_price ne 0}
<font class="ProductDetailsTitle">{include file='common/currency.tpl' value=$product.taxed_price}</font><font class="MarketPrice"> {include file='common/alter_currency_value.tpl' alter_currency_value=$product.taxed_price}</font>
{if $product.taxes}<br />{include file="customer/main/taxed_price.tpl" taxes=$product.taxes}{/if}
{else}
<input type="text" size="7" name="price" />
{/if}
	</td>
</tr>
</table>
	</td>
</tr>
</table>
{/capture}
{include file="common/section.tpl" title=$product.producttitle content=$smarty.capture.section}

{if $addons.detailed_product_images ne ""}
<br />
{include file="addons/detailed_product_images/product_images.tpl" }
{/if}

<p />
{capture name=section}
<p />
{$lng.txt_product_html_code_comment}
<p align="center"><b>{$catalogs.customer}{pages_url var="product" product_id=$product.product_id salesman=$customer_id}</b></p>

{if $banners ne ''}
<center>
<table cellpadding="2" cellspacing="3" width="100%">
{foreach from=$banners item=v}
<tr>
	<th class="TableHead">{$v.banner}</th>
</tr>
<tr>
	<td align="center">
{capture name="html_1"}{include file="main/display_banner.tpl" banner=$v type="js" salesman=$customer_id product_id=$product.product_id}{/capture}
<p>{$smarty.capture.html_1|amp}</p>

	</td>
</tr>
<tr>
    <td align="center"><b>{$lng.lbl_iframe_code}:</b></td>
</tr>
<tr>
    <td align="center"><textarea cols="65" rows="6">{include file="main/display_banner.tpl" assign="ban" banner=$v type="iframe" salesman=$customer_id product_id=$product.product_id current_location=$http_location}{$ban|escape}</textarea></td>
</tr>
<tr>
	<td align="center"><b>{$lng.lbl_javascript_version}:</b></td>
</tr>
<tr>
	<td align="center"><textarea cols="65" rows="6">{$smarty.capture.html_1|escape}</textarea></td>
</tr>
<tr>
	<td><hr size="1" noshade="noshade" align="center" /></td>
</tr>
{/foreach}
</table>
</center>
{/if}
{/capture}
{include file="common/section.tpl" title=$lng.lbl_product_html_code content=$smarty.capture.section}
<p />

{include file="addons/wholesale_trading/product_wholesale_salesman.tpl"}
