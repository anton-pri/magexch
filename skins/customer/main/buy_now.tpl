{if $product.price gt 0}
{* kornev, TOFIX *}
{if $config.Product_Options.buynow_with_options_enabled eq 'Y' || ($product.avail eq 0 && $product.variant_id)}
{assign var="buynow_enabled" value=false}
{else}
{assign var="buynow_enabled" value=true}
{/if}
<form name="orderform_{$product.product_id}_{$product.add_date}" method="post" action="{if $product.is_product_options eq 'Y' && !$buynow_enabled}{pages_url var="product" product_id=$product.product_id}{else}{$current_location}/index.php?target=cart&amp;mode=add{/if}">
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="cat" value="{$smarty.get.cat|escape:"html"}" />
<input type="hidden" name="page" value="{$smarty.get.page|escape:"html"}" />
{/if}

{if $product.price eq 0}
{assign var="button_href" value=$smarty.get.page|escape:"html"}
{capture name=product_url}{pages_url var="product" product_id=$product.product_id cat=$cat page=$button_href}{/capture}
{include file="buttons/buy_now.tpl" style='btn' href=$smarty.capture.product_url}
{else}
<div class="qty_compact">

{if $product.is_product_options ne 'Y' || $buynow_enabled}
{if !$product.distribution}
{if !($product.avail le 0 or $product.avail lt $product.min_amount)}
{$lng.lbl_quantity}
{/if}
{if ($product.avail le 0 or $product.avail lt $product.min_amount)}
<b>{$lng.txt_out_of_stock}</b>
{else}
{if $config.General.unlimited_products eq "Y"}
{assign var="mq" value=$config.Appearance.max_select_quantity}
{else}
    {if $product.min_amount gt 1}
      {math equation="x/y" x=$config.Appearance.max_select_quantity y=$product.min_amount assign="tmp"}
    {else}
      {assign var='tmp' value=$config.Appearance.max_select_quantity}
    {/if}
{if $tmp<2}
{assign var="minamount" value=$product.min_amount} 
{else} 
{assign var="minamount" value=1}
{/if} 
{math equation="min(maxquantity+minamount, productquantity+1)" assign="mq" maxquantity=$config.Appearance.max_select_quantity minamount=$minamount productquantity=$product.avail}
{/if}
{if $product.min_amount le 1}
{assign var="start_quantity" value=1}
{else}
{assign var="start_quantity" value=$product.min_amount}
{/if}
{if $config.General.unlimited_products eq "Y"}
{math equation="x+y" assign="mq" x=$mq y=$start_quantity}
{/if}
<select name="amount">
{section name=quantity loop=$mq start=$start_quantity}
	<option value="{%quantity.index%}"{if $smarty.get.quantity eq %quantity.index%} selected="selected"{/if}>{%quantity.index%}</option>
{/section}
</select>
{/if}
{else}
<input type="hidden" name="amount" value="1" />
{/if}
<input type="hidden" name="action" value="add" />
{elseif $product.distribution eq "" && $config.General.unlimited_products ne "Y" && ($product.avail le 0 or $product.avail lt $product.min_amount) && !$product.variant_id}
<b>{$lng.txt_out_of_stock}</b>
{/if}
</div>
{if $config.General.unlimited_products eq "Y" || ($product.avail gt 0 and $product.avail ge $product.min_amount) || $product.variant_id}
{include file='buttons/add_to_cart.tpl' style='btn' href="javascript: cw_submit_form('orderform_`$product.product_id`_`$product.add_date`');"}
{if $addons.estore_gift && $set_view ne "2" && ($product.is_product_options ne 'Y' || $buynow_enabled)}
{include file='buttons/add_to_wishlist.tpl'  href="javascript: cw_submit_form('orderform_`$product.product_id`_`$product.add_date`', 'add2wl')"}
{/if}
{/if}
{if $product.min_amount gt 1}
{$lng.txt_need_min_amount|substitute:"items":$product.min_amount}
{/if}



{/if}
{if $product.price gt 0}
</form>
{/if}
