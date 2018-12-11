<!-- cw@cart_titles [ -->

<!-- cw@cart_titles ] -->

{foreach from=$products item=product}
{if !$product.hidden}
<tr product_id='{$product.product_id}' cartid='{$product.cartid}'>
<!-- cw@cart_image [ -->
    <td class="cart_product">
    <div class="image">
        <a href="{pages_url var="product" product_id=$product.product_id}">{include file='common/product_image.tpl' product_id=$product.product_id image=$product.image_thumb id='product_thumbnail'}</a>
    </div>
    </td>
<!-- cw@cart_image ] -->

<!-- cw@cart_name [ -->
    <td class="cart_name">
    <h1 class="product_name"> <a class="product" href="{pages_url var="product" product_id=$product.product_id}">{$product.product}</a></h1>
    {*<div class="descr">{$product.descr|truncate:200|strip_tags}</div>*}
<<<<<<< Updated upstream
    <div class="CartSellerData">
    <table border="0" cellspacing="0" cellpadding="2" width="100%" class="SellerTable">
    <tbody>
       <tr class="ProductTableHead">
	  <td align="center" width="120">Condition</td>
	  <td align="center">Seller's Description</td>
	  <td align="center" width="170">Seller Information</td>
       </tr>
    {tunnel func='cw_seller_get_info' via='cw_call' assign='seller_info' param1=$product.seller_id}
    {tunnel func='cw\custom_magazineexchange_sellers\mag_product_seller_item_data' via='cw_call' assign='seller_data' param1=$product.seller_item_id}
       <tr>
	  <td align="center">{tunnel func='cw_get_langvar_by_name' via='cw_call' param1="lbl_mag_seller_product_condition_`$seller_data.condition`" param2='' param3=false param4=true}</td>
	
	  <td align="center">{$seller_data.comments|nl2br}</td>
	  <td align="center"><span class="SellerName">{$seller_info.name}</span><br></td>
      </tr>
    </tbody>
    </table>
    </div>
=======
    {include file="customer/cart/cart_seller.tpl"}
>>>>>>> Stashed changes
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
<!-- cw@cart_name ] -->

<!-- cw@cart_avail [ -->
          {*if $product.avail gt 0}
            <div class="in_stock">{$lng.lbl_in_stock}</div>
          {else}
            <div class="out_of_stock">{$lng.lbl_out_of_stock}</div>
          {/if*}
<!-- cw@cart_avail ] -->

<!-- cw@cart_price [ -->
        {assign var="price" value=$product.display_price}
        <span id="cart_item_price_{$product.cartid}" class="price">{include file='common/currency.tpl' value=$price} x</span>
<!-- cw@cart_price ] -->

<!-- cw@cart_qty [ -->
         
        {if $addons.egoods and $product.distribution || $from_quote}{$product.amount}<input type="hidden"{else}<input type="text" size="3"{/if} name="productindexes[{$product.cartid}]" value="{$product.amount}" id="productindexes_{$product.cartid}" {if $use_ajax} onChange="javascript: ajax_update_cart();"{/if} >
<!-- cw@cart_qty ] -->

<!-- cw@cart_total [ -->
        =
        {math equation="price*amount" price=$price amount=$product.amount format="%.2f" assign=unformatted}
        <span id="cart_item_total_{$product.cartid}" class="price total_price">{include file='common/currency.tpl' value=$unformatted}</span><br />
        <font class="MarketPrice"> 
            <span id="cart_item_alter_{$product.cartid}">{include file='common/alter_currency_value.tpl' alter_currency_value=$unformatted}</span>
        </font>
        {if $config.Taxes.display_taxed_order_totals eq "Y" and $product.taxes}<span id="cart_item_taxes_{$product.cartid}">{include file="customer/main/taxed_price.tpl" taxes=$product.taxes}</span>
        {/if}
<!-- cw@cart_total ] -->

<!-- cw@cart_delete [ -->
      {if !$from_quote}
         <div class="cart_product_buttons">
	    {include file="buttons/update.tpl" href="javascript: cw_submit_form('cartform')" } <div class="update_qty_note">{$lng.lbl_update_qty_note}</div>

	    {include file='buttons/delete_item.tpl' href="index.php?target=`$current_target`&amp;action=delete&amp;productindex=`$product.cartid`"}
         </div>
      {/if}
    </td>
<!-- cw@cart_delete ] -->

</tr>
{/if}
{/foreach}
