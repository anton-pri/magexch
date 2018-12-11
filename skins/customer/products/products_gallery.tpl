{if $products}

{if $config.Appearance.products_per_row eq 1}{assign var="gallery_product_item_width" value="96%"}
{elseif $config.Appearance.products_per_row eq 2}{assign var="gallery_product_item_width" value="46%"}
{else}{assign var="gallery_product_item_width" value="predef"}{/if}

{if $gallery_product_item_width ne "predef"}
{literal}
<style type="text/css">
.product_item { width:{/literal}{$gallery_product_item_width}{literal}; }
</style>
{/literal}
{/if}


<div class="products_gallery">

<div class="prod_gallery" id="product_list">


    {foreach from=$products item=product}
    <article class="product_item item">
      <div class="prod_border">
        <div class="g-image">
            <a href="{pages_url var="product" product_id=$product.product_id page=$navigation_page}" class="image">
              {include file='common/product_image.tpl' image=$product.image_thumb product_id=$product.product_id html_height=$config.Appearance.products_images_thumb_height no_img_id='Y'}
            </a>
        </div>
        <div class="name_price">
          <!-- cw@gallery_title [ -->
          <div class="g_product"><a href="{pages_url var="product" product_id=$product.product_id page=$navigation_page}" class="product">{$product.product|truncate:55}</a></div>
          <!-- cw@gallery_title ] -->

          {if $product.display_price gt 0}

          <div class="price">
            {if $product.list_price gt "0"}
            <div class="list_price">
            <!-- cw@list_price [ -->
              <span>{include file='common/currency.tpl' value=$product.list_price}</span>
            <!-- cw@list_price ] -->
            </div>
            {/if}

            {*<label>{$lng.lbl_our_price}</label>*}
            <!-- cw@our_price [ -->
            <div class="our_price">
            {include file='common/currency.tpl' value=$product.display_price}
            {include file='common/alter_currency_value.tpl' alter_currency_value=$product.display_price}
            </div>
            <!-- cw@our_price ] -->

          </div>

          {else}
            <div class="price"><label>{$lng.lbl_enter_your_price}</label></div>
          {/if}

          {if $usertype eq 'C' and $config.Appearance.buynow_button_enabled eq "Y"}<div class="buy_now">{include file="customer/main/buy_now_list.tpl" product=$product}</div>{/if}

          <!-- cw@in_stock [ -->
          {if $product.avail gt 0}
            <div class="in_stock">{$lng.lbl_in_stock}</div>
          {else}
            <div class="out_of_stock">{$lng.lbl_out_of_stock}</div>
          {/if}
          <!-- cw@in_stock ] -->
        </div>

      </div>
    </article>
    {/foreach}

</div>

</div>
<div class="clear"></div>
{else}
{$lng.txt_no_products_found}
{/if}
