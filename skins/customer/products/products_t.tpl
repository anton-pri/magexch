{if $config.Appearance.products_per_row gt 0}
{assign var="products_per_row" value=$config.Appearance.products_per_row}
{else}
{assign var="products_per_row" value=4}
{/if}
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td>

<table width="100%" cellpadding="5" cellspacing="1">

{math equation="floor(100/x)" x=$products_per_row assign="width"}

{section name=product loop=$products}
{assign var="discount" value=0}

{if %product.index% is div by $products_per_row}
<tr>
{assign var="cell_counter" value=0}
{/if}

{math equation="x+1" x=$cell_counter assign="cell_counter" }

	<td width="{$width}%" class="PListCell">

<a href="{pages_url var="product" product_id=$products[product].product_id cat=$cat page=$navigation_page}{if $featured}&amp;featured=Y{/if}" class="ProductTitle">{$products[product].product}</a><br />
{if $config.Appearance.display_productcode_in_list eq "Y" and $products[product].productcode ne ""}
{$lng.lbl_sku}: {$products[product].productcode}<br />
{/if}
<table cellpadding="3" cellspacing="0" width="100%">
<tr>
	<td height="100" nowrap="nowrap">
<a href="{pages_url var="product" product_id=$products[product].product_id cat=$cat page=$navigation_page}{if $featured}&amp;featured=Y{/if}">
    {include file='common/product_image.tpl' image=$products[product].image_thumb product_id=$products[product].product_id}
</a>
	</td>
</tr>
</table>
<a href="{pages_url var="product" product_id=$products[product].product_id cat=$cat page=$navigation_page}{if $featured}&amp;featured=Y{/if}">{$lng.lbl_see_details}</a>
<br />
<br />
{if ($products[product].avail le 0 or $products[product].avail lt $products[product].min_amount) && $products[product].variant_id}
&nbsp;
{elseif $products[product].taxed_price ne 0}
{if $products[product].list_price gt 0 and $products[product].taxed_price lt $products[product].list_price}
{math equation="100-(price/lprice)*100" price=$products[product].price lprice=$products[product].list_price_net assign=discount}
{if $discount gt 0}
<font class="MarketPrice">{$lng.lbl_market_price}: <s>
{include file='common/currency.tpl' value=$products[product].list_price}
</s></font><br />
{/if}
{/if}
<font class="ProductPrice">{$lng.lbl_our_price}: {include file='common/currency.tpl' value=$products[product].taxed_price}</font><br /><font class="MarketPrice">{include file='common/alter_currency_value.tpl' alter_currency_value=$products[product].taxed_price}</font>{if $discount gt 0}{if $config.General.alter_currency_symbol ne ""},{/if} {$lng.lbl_save_price} {$discount}%{/if}
{if $products[product].taxes}<br />{include file="customer/main/taxed_price.tpl" taxes=$products[product].taxes}{/if}
{else}
<font class="ProductPrice">{$lng.lbl_enter_your_price}</font>
{/if}
{if $addons.Feature_Comparison ne '' && $products[product].fclass_id > 0}
<div align="center" style="width: 100%; padding-top: 10px;">
{include file="addons/Feature_Comparison/compare_checkbox.tpl" id=$products[product].product_id}
</div>
{/if}
{* 'Buy Now' button 
{if $usertype eq "C" and $config.Appearance.buynow_button_enabled eq "Y"}
{include file="customer/main/buy_now.tpl" product=$products[product]}
{/if}
 'Buy Now' button ***}
	</td>

{capture name=prod_index}
{math equation="index+x+1" index=%product.index% x=$products_per_row}
{/capture}
{if $smarty.capture.prod_index is div by $products_per_row }
</tr>
{/if}

{/section}

{if $cell_counter lt $products_per_row}
{section name=rest_cells loop=$products_per_row start=$cell_counter}
	<td class="SectionBox">&nbsp;</td>
{/section}
</tr>
{/if}

</table>
	</td>
</tr>
</table>
