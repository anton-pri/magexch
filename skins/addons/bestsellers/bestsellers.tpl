{if $bestsellers}
{capture name=bestsellers}
<table cellpadding="0" cellspacing="2">
{foreach from=$bestsellers item=bestseller}
<tr>
{if $config.bestsellers.bestsellers_thumbnails eq "Y"}
	<td width="30">
	<a href="{pages_url var="product" product_id=$bestseller.product_id cat=$cat bestseller='Y'}">{include file='common/thumbnail.tpl' image=$bestseller.image_thumb}</a>
	</td>
{/if}
	<td>
	<b><a href="{pages_url var="product" product_id=$bestseller.product_id cat=$cat bestseller='Y'}">{$bestseller.product}</a></b><br />
{$lng.lbl_our_price}: {include file='common/currency.tpl' value=$bestseller.taxed_price}<br />
	</td>
</tr>
{/foreach}
</table>
{/capture}
{include file="common/section.tpl" title=$lng.lbl_bestsellers content=$smarty.capture.bestsellers extra='width="100%"'}
{/if}
