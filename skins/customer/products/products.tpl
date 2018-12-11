{if $products}
<div class="standart_products" id="product_list">
{foreach from=$products item=product}
<article class="product_info item"> 
  <div class="wrapper">
    <div class="standart_image">
        <a href="{if $current_area eq 'B'}index.php?target=product&amp;product_id={$product.product_id}{else}{pages_url var="product" product_id=$product.product_id cat=$cat page=$navigation_page featured=$featured}{/if}" class="image">
          {include file='common/product_image.tpl' image=$product.image_thumb product_id=$product.product_id html_height=$config.Appearance.products_images_thumb_height no_img_id='Y'}
        </a>
    </div>
    <div class="main_info">
        {include file='customer/products/products-info.tpl'}
    </div>
    <div class="info_box">

     <div class="prod_prices">
        {if $product.display_price gt 0}
        <div class="price">
            {include file='common/currency.tpl' value=$product.display_price}
            {if $product.list_price gt 0}
              <span class='list_price'>{include file='common/currency.tpl' value=$product.list_price}</span>
            {/if}
            {include file='customer/products/discount.tpl'}

            {include file='common/alter_currency_value.tpl' alter_currency_value=$product.display_price}
        </div>
        {else}
            <div class="price"><label>{$lng.lbl_enter_your_price}</label></div>
        {/if}
     </div>
     
        {if $usertype eq 'C' and $config.Appearance.buynow_button_enabled eq 'Y'}
          <div class="prod_buttons">
            {include file='customer/main/buy_now.tpl'}
          </div>
        {/if}
    </div>


    <div class="clear"></div>
  </div>
</article>
{/foreach}
</div>

{else}
{$lng.txt_no_products_found}
{/if}
