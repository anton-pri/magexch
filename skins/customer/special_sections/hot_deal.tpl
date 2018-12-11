{if $product}
{capture name=section}
{pages_url var='product' product_id=$product.product_id assign='product_url'}
<form name="orderform_hot_deal" method="post" action="{$current_location}/index.php?target=cart&amp;mode=add">
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="amount" value="1">
<input type="hidden" name="action" value="add" />

<div class="hot_deal standart_products" id="product_list">
<article class="product_info item"> 
  <div class="wrapper">
    <div class="standart_image">
        <a href="{if $current_area eq 'B'}index.php?target=product&amp;product_id={$product.product_id}{else}{pages_url var="product" product_id=$product.product_id cat=$cat page=$navigation_page featured=$featured}{/if}" class="image">
          {include file='common/product_image.tpl' image=$product.image_thumb product_id=$product.product_id no_img_id='Y'}
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
              {include file='common/currency.tpl' value=$product.list_price}
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
</div>
</form>
{/capture}
{include file='common/section.tpl' title=$lng.lbl_hot_deal_ex content=$smarty.capture.section style='hot_deal' is_dialog=1}
{/if}
