{foreach from=$products item=product}
{if !$product.hidden}
<div class="product_info">
    <div class="image">
        <a href="{pages_url var="product" product_id=$product.product_id}">{include file='common/product_image.tpl' product_id=$product.product_id image=$product.image_thumb id='product_thumbnail'}</a>
    </div>
    <div class="left cart-product">
    <div class="product_name"> <a class="product" href="{pages_url var="product" product_id=$product.product_id}">{$product.product}</a></div>
    {if $user_account.insider}
        {include file='customer/main/cart_warehouse_selection.tpl' product=$product}
    {/if}

{* kornev, TOFIX *}
    {if $product.product_options}
	    <div class="product_field{cycle values=", cycle"}">
	    <label>{$lng.lbl_selected_options}</label>
	    {include file='addons/product_options/main/options/display.tpl' options=$product.product_options}
	    </div>
    {/if}

    {include file='customer/cart/item_price.tpl' product=$product}
    </div>
    <div class="p_cart_buttons">
       {if !$from_quote}
	    <div class="delete_button">{include file='buttons/delete_item.tpl' href="index.php?target=`$current_target`&amp;action=delete&amp;productindex=`$product.cartid`"}</div>
	{* kornev, TOFIX *}
	    {if $product.product_options ne ''}
	    <div class="edit_button">{include file='buttons/edit_product_options.tpl' id=$product.cartid}</div>
	    {/if}
	{/if}
    </div>
    <div class="clear"></div>

</div>
{/if}
{/foreach}
