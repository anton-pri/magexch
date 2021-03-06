{if $clearance}
{capture name=section}
<p>{$lng.txt_clearance_msg}</p>
<div class="products_gallery">
<div class="prod_gallery" id="product_list">
{foreach from=$clearance item=product}
    <article class="product_item item">
      <div class="prod_border">
        <div class="g-image">
            <a href="{pages_url var="product" product_id=$product.product_id cat=$cat page=$navigation_page}" class="image">
              {include file='common/product_image.tpl' image=$product.image_thumb product_id=$product.product_id html_height=$config.Appearance.products_images_thumb_height no_img_id='Y'}
            </a>
        </div>
        <div class="name_price">
          <div class="g_product"><a href="{pages_url var="product" product_id=$product.product_id cat=$cat page=$navigation_page}" class="product">{$product.product|truncate:55}</a></div>

          {if $product.display_price gt 0}

          <div class="price">
            {if $product.list_price gt "0"}<div class="list_price"><span>{include file='common/currency.tpl' value=$product.list_price}</span></div>{/if}

            {*<label>{$lng.lbl_our_price}</label>*}
            <div class="our_price">
            {include file='common/currency.tpl' value=$product.display_price}
            {include file='common/alter_currency_value.tpl' alter_currency_value=$product.display_price}
            </div>
          </div>

          {else}
            <div class="price"><label>{$lng.lbl_enter_your_price}</label></div>
          {/if}

          {if $usertype eq 'C' and $config.Appearance.buynow_button_enabled eq "Y"}<div class="buy_now">{include file="customer/main/buy_now_list.tpl" product=$product}</div>{/if}
          {if $product.avail gt 0}
            <div class="in_stock">{$lng.lbl_in_stock}</div>
          {else}
            <div class="out_of_stock">{$lng.lbl_out_of_stock}</div>
          {/if}
        </div>

      </div>
    </article>

{/foreach}
</div>
</div>

{/capture}
{include file='common/section.tpl' title=$lng.lbl_clearance content=$smarty.capture.section style='clearance' is_dialog=1}
{/if}
