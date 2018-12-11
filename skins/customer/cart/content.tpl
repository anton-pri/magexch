<!-- cw@cart_titles [ -->
<tr>
    <th class="cart_product first_item">{$lng.lbl_product}</th>
    <th class="cart_description item">{$lng.lbl_description}</th>
    <th class="cart_avail item">{$lng.lbl_avail}.</th>
    <th class="cart_unit item">{$lng.lbl_unit_price}</th>
    <th class="cart_quantity item">{$lng.lbl_qty}</th>
    <th class="cart_total item">{$lng.lbl_total}</th>
    <th class="cart_delete last_item">&nbsp;</th>
</tr>
<!-- cw@cart_titles ] -->

{foreach from=$products item=product}
{if !$product.hidden}
<tr product_id='{$product.product_id}' cartid='{$product.cartid}'>
<!-- cw@cart_image [ -->
    <td class="cart_product">
    <div class="image">
        <a href="{pages_url var="product" product_id=$product.product_id}">{include file='common/product_image.tpl' product_id=$product.product_id image=$product.image_thumb id="cart_product_thumbnail_`$product.cartid`"}</a>
    </div>
    </td>
<!-- cw@cart_image ] -->

<!-- cw@cart_name [ -->
    <td class="cart_name">
    <h1 class="product_name"> <a class="product" href="{pages_url var="product" product_id=$product.product_id}">{$product.product}</a></h1>
    {*<div class="descr">{$product.descr|truncate:200|strip_tags}</div>*}

    {if $user_account.insider}
        {include file='customer/main/cart_warehouse_selection.tpl' product=$product}
    {/if}

{* kornev, TOFIX *}
    {if $product.product_options}
	    <div class="product_field{cycle values=", cycle"}">
	    {include file='addons/product_options/main/options/display.tpl' options=$product.product_options}
	    </div>
       {if !$from_quote}
	    {if $product.product_options ne ''}
	        {include file='buttons/edit_product_options.tpl' id=$product.cartid}
	    {/if}
	{/if}
    {/if}
    </td>
<!-- cw@cart_name ] -->

<!-- cw@cart_avail [ -->
    <td class="cart_avail">
          {if $product.avail gt 0}
            <div class="in_stock">{$lng.lbl_in_stock}</div>
          {else}
            <div class="out_of_stock">{$lng.lbl_out_of_stock}</div>
          {/if}
    </td>
<!-- cw@cart_avail ] -->

<!-- cw@cart_price [ -->
    <td data-title="Unit price">
        {assign var="price" value=$product.display_price}
        <span id="cart_item_price_{$product.cartid}" class="price">{include file='common/currency.tpl' value=$price}</span>
    </td>
<!-- cw@cart_price ] -->

<!-- cw@cart_qty [ -->

    <td class="qty">
        {if $addons.egoods and $product.distribution || $from_quote}{$product.amount}<input type="hidden"{else}<input type="text" size="3"{/if} name="productindexes[{$product.cartid}]" value="{$product.amount}" id="productindexes_{$product.cartid}" {if $use_ajax} onChange="javascript: ajax_update_cart();"{/if} >
    </td>
<!-- cw@cart_qty ] -->

<!-- cw@cart_total [ -->

    <td data-title="Total">
        {math equation="price*amount" price=$price amount=$product.amount format="%.2f" assign=unformatted}
        <span id="cart_item_total_{$product.cartid}" class="price">{include file='common/currency.tpl' value=$unformatted}</span><br />
        <font class="MarketPrice"> 
            <span id="cart_item_alter_{$product.cartid}">{include file='common/alter_currency_value.tpl' alter_currency_value=$unformatted}</span>
        </font>
        {if $config.Taxes.display_taxed_order_totals eq "Y" and $product.taxes}<span id="cart_item_taxes_{$product.cartid}">{include file="customer/main/taxed_price.tpl" taxes=$product.taxes}</span>
        {/if}
    </td>
<!-- cw@cart_total ] -->

<!-- cw@cart_delete [ -->
    <td class="delete">
      {if !$from_quote}
	    {include file='buttons/delete_item.tpl' href="index.php?target=`$current_target`&amp;action=delete&amp;productindex=`$product.cartid`"}
      {/if}
    </td>
<!-- cw@cart_delete ] -->

</tr>
{/if}
{/foreach}
