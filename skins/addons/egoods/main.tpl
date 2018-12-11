<p />
{capture name=section}
<p />
{if $product}
<table width="100%">
<tr>
	<td colspan="2" class="ProductTitle"><a href="{pages_url var="product" product_id=$product.product_id}">#{$product.product_id}. {$product.product}</a></td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
<tr>
	<td valign="top" align="left" rowspan="2" width="120">
<a href="{pages_url var="product" product_id=$product.product_id}">{include file='common/thumbnail.tpl' image=$product.image}</a>
	</td>
	<td valign="top">

<span>{if $product.fulldescr ne ""}{$product.fulldescr}{else}{$product.descr}{/if}</span>
	<p />

{include file="common/subheader.tpl" title=$lng.lbl_details}

<table width="100%" cellpadding="0" cellspacing="0">
{if $product.weight ne "0.00"}
<tr>
	<td width="30%">{$lng.lbl_weight}</td>
	<td nowrap="nowrap">{$product.weight} {$config.General.weight_symbol}</td>
</tr>
{/if}
</table>

	</td>
</tr>
<tr>
	<td>
<br /><br />
{$lng.lbl_download_msg}
<br /><br />
{assign var="title_length" value=""}
{if $product.length > 0}
{assign var="title_length" value=$lng.lbl_file_size|cat:": `$product.length` `$lng.lbl_byte`"}
{/if}
{include file='buttons/button.tpl' button_title=$lng.lbl_download href=$url title=$title_length}<br />
{$title_length}
	</td>
</tr>
</table>
{else}
{$lng.lbl_download_errmsg}
{/if}
{/capture}
{include file="common/section.tpl" title=$lng.lbl_download content=$smarty.capture.section}
