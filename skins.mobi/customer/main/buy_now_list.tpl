{if $current_area eq 'B'}
    {include file='buttons/button.tpl' button_title=$lng.lbl_details style='btn' href='index.php?target=products&mode=details&product_id=`$product.product_id`'}

{elseif $product.product_type eq 10}
    {pages_url var="product" product_id=$products[product].product_id cat=$cat page=$navigation_page assign='product_url'}
    {include file='buttons/button.tpl' button_title=$lng.lbl_details style='btn' href=$product_url}

{elseif $product.price eq 0}
{pages_url var='product' product_id=$product.product_id cat=$cat page=$button_href assign='product_url'}
{include file="buttons/buy_now.tpl" style='btn' href=$product_url}

{else}
    {if $product.is_product_options eq 'Y' && !$buynow_enabled}
        {pages_url var='product' product_id=$product.product_id assign='product_url'}
    {else}
        {assign var='product_url' value="`$current_location`/index.php?target=cart&amp;mode=add"}
    {/if}
<form name="buy_now_list_{$product.product_id}_{$buy_now_postfix}" method="post" action="{$product_url}" {if $target}target="{$target}"{/if}>
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="cat" value="{$smarty.get.cat|escape:"html"}" />
<input type="hidden" name="page" value="{$smarty.get.page|escape:"html"}" />
{if $product.is_product_options ne 'Y' || $buynow_enabled}
    {if !$with_amount}
<input type="hidden" name="amount" value="1" />
    {/if}
<input type="hidden" name="action" value="add" />

{elseif $product.distribution eq "" && !($addons.subscriptions ne "" and $products[product].catalogprice) && $config.General.unlimited_products ne "Y" && ($product.avail le 0 or $product.avail lt $product.min_amount) && !$product.variant_id}
	<p>{$lng.txt_out_of_stock}</p>
{/if}

{if ($config.General.unlimited_products eq "Y" || ($product.avail gt 0 and $product.avail ge $product.min_amount) || $product.variant_id) && $with_amount}
    &nbsp;<input name="amount" size="4" value="1">&nbsp;
{/if}
{if $config.General.unlimited_products eq "Y" || ($product.avail gt 0 and $product.avail ge $product.min_amount) || $product.variant_id}
    {if $target}
{include file='buttons/buy_now.tpl' style='btn' href="javascript: cw_submit_form('buy_now_list_`$product.product_id`_`$buy_now_postfix`'); window.close();"}
    {else}
{include file='buttons/buy_now.tpl' style='btn' href="javascript: cw_submit_form('buy_now_list_`$product.product_id`_`$buy_now_postfix`');"}
    {/if}
{/if}
{if $addons.estore_gift}
<div class="wishlist">{include file='buttons/button.tpl'  button_title=$lng.lbl_wishlist_mobile href="javascript: cw_submit_form('buy_now_list_`$product.product_id`_`$buy_now_postfix`', 'add2wl');"}</div>
{/if}

{if $product.min_amount gt 1}
	{$lng.txt_need_min_amount|substitute:"items":$product.min_amount}
{/if}

{if $product.price gt 0}
</form>
{if $target eq "parent"}
<script language="javascript">
if (opener.name)
    document.getElementById('form_buy_now_list_{$product.product_id}_{$buy_now_postfix}').target=opener.name;
</script>
{/if}
{/if}

{/if}
