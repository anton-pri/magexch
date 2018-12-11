<div class="products_gallery deals_products">
<div class="prod_gallery" id="product_list">
{foreach from=$products item=product name='dow'}
<article class="product_item item">
<div class="prod_border {if $smarty.foreach.dow.last} last{/if}"{if $is_week} id="week_page_{$smarty.foreach.dow.index}" style="display:none"{/if}>
{if $is_week}
<script language="javascript">
    week_pages.push('week_page_{$smarty.foreach.dow.index}');
</script>
{/if}

        <div class="g-image">
            <a href="{pages_url var="product" product_id=$product.product_id cat=$cat page=$navigation_page}" class="image">
              {include file='common/product_image.tpl' image=$product.image_thumb product_id=$product.product_id no_img_id='Y'}
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
