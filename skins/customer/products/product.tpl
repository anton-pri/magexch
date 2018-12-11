{include_once_src file='main/include_js.tpl' src='customer/images/popup_image.js'}
{include_once file='js/form_validation_js.tpl'}

{assign var=js_tab value=$js_tab|default:$smarty.get.js_tab}
<!-- cw@products_in_category [ -->

{if $config.Appearance.categories_in_products}
	<div style="text-align:right;padding-bottom:10px;" class="back_to_category">
		<a href="{$product.category_category_url}?view_all=all">{$lng.lbl_view_all_products_in_category}</a>
	</div>
{/if}
<!-- cw@products_in_category ] -->

<div class="product_view" itemscope itemtype="http://schema.org/Product">
<!-- cw@product_dialog [ -->
{capture name=dialog}
<div class="product">

<form name="order_form" method="post" action="{$current_location}/index.php?target=cart&amp;mode=add" onsubmit="javascript: return FormValidation();" id='order_form'>
<input type="hidden" name="action" value="add" />
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="cat" value="{$smarty.get.cat|escape:"html"}" />
<input type="hidden" name="page" value="{$smarty.get.page|escape:"html"}" />

<!-- cw@product_image [ -->
    <div class="image">
    {include file='customer/products/thumbnail.tpl' image=$product.image_det id='product_thumbnail'}




{if $product.pdf_link}
    <a href="{$product.pdf_link}" class="download_pdf">{$lng.lbl_download_pdf}</a>
    <a href="{$config.General.acrobat_link}" target="_blank" class="download_pdf">{$lng.lbl_get_acrobat}</a>
    <a href="{$config.General.acrobat_link}" target="_blank"><img src="{$ImagesDir}/pdf_icon.gif" width="15" height="14" alt="" /></a>
{/if}
    </div>
<!-- cw@product_image ] -->


    <div class="fields">
        <!-- cw@product_name [ -->
        <h1 class="product_name" itemprop="name">{$product.product}</h1>
        <!-- cw@product_name ] -->

        <!-- cw@product_description [ -->
        <div class="descr">
        {if $product.descr|default:$product.fulldescr|count_characters < ($config.Appearance.short_descr_truncate+50)}
           {$product.descr|default:$product.fulldescr}
        {else}
            {$product.descr|default:$product.fulldescr|truncate:$config.Appearance.short_descr_truncate|strip_tags:true}
        {/if}
            {if $product.fulldescr|count_characters >  $product.descr|count_characters || $product.descr|count_characters > ($config.Appearance.short_descr_truncate+50) }<a class="full_descr_anchor" href="#" onclick="javscript: goToAnchor('product_description');">{$lng.lbl_read_more}</a>{/if}
        </div>
        <!-- cw@product_description ] -->

        {include file='customer/products/availability.tpl' product=$product}


    {if $config.Appearance.show_views_on_product_page eq "Y"}
        <div><span style="color: #808080;">{$lng.lbl_number_of_views}:</span> {$product.views_stats}</div>
    {/if}

    <!-- cw@product_social [ -->
    {if $config.Appearance.social_buttons eq 'Y'}{include file='customer/products/social.tpl'}{/if}
    <!-- cw@product_social ] -->

    <!-- cw@product_rating [ -->
    {include file="customer/products/product_rating.tpl"}
    <!-- cw@product_rating ] -->

    <!-- cw@send_to_friend [ -->
    {if $config.Appearance.send_to_friend_enabled eq 'Y'}
      <a href="index.php?target=popup_sendfriend&amp;product_id={$product.product_id}" class="ajax send_to_friend" id='send_to_friend_link' blockUI='send_to_friend_link'><i class="send_to_friend_icon"></i>{$lng.lbl_send_to_friend}</a><div id='send_to_friend_dialog' style="display:none;"></div>
    {/if}
    <a href="javascript:print();" class="print"><i class="print_icon"></i>{$lng.lbl_print}</a>
    <!-- cw@send_to_friend ] -->


    {include file='customer/tags/tags.tpl' tags=$product.tags}
    {include file='customer/products/additional_data.tpl'}
    </div>

    <!-- cw@product_actions [ -->

    <div class="product_add">
    <div class="wrapper">
    <!-- cw@product_prices [ -->
        {include file='customer/products/product_price.tpl'}
    <!-- cw@product_prices ] -->

    <!-- cw@product_options [ -->
        <div class="options">
            {*include file='common/subheader.tpl' title=$lng.lbl_options*}
            {include file='customer/products/product-amount.tpl'}
        </div>
    <!-- cw@product_options ] -->

    <!-- cw@product_buttons [ -->
        <div class="buttons">
            {include file='buttons/add_to_cart.tpl' button_title=$lng.lbl_add_to_cart style='btn' href="javascript: if(FormValidation()) cw_submit_form('order_form');"}
            <div class="box-security"><img src="{$ImagesDir}/payment-logo.png" alt="" class="payments" /></div>
            {if $addons.estore_gift}
            <div class="wish_wrapper">{include file='buttons/add_to_wishlist.tpl'  href="javascript: if (FormValidation()) cw_submit_form('order_form', 'add2wl');"}</div>
            {/if}
        </div>
    <!-- cw@product_buttons ] -->

    </div>
    </div>
    <!-- cw@product_actions ] -->

    <div class="clear"></div>

</form>

<!-- cw@product_extra -->


</div>
{/capture}
{$smarty.capture.dialog}
<!-- cw@product_dialog ] -->

<!-- cw@product_tabs [ -->
{jstabs name='product_data_customer'}
default_tab={$js_tab|default:"1"}
default_template="customer/products/product_tabs.tpl"

[1]
title="{$lng.lbl_product_summary}"

{if $attributes}
[2]
title="{$lng.lbl_features}"
template="customer/products/feature_tab.tpl"
{/if}

{if $product.specifications}
[3]
title="{$lng.lbl_specifications}"
{/if}

{*if $addons.estore_products_review}
[4]
title="{$lng.lbl_customers_reviews}"
template="addons/estore_products_review/vote_reviews.tpl"
{/if*}

{/jstabs}
{include file='tabs/js_tabs.tpl'}

<!-- cw@product_tabs ] -->


{if $addons.estore_products_review}
<section>
<a name="write_rev"></a>
{include file="addons/estore_products_review/vote_reviews.tpl"}
</section>
{/if}

{*if $addons.recommended_products}
{include file='addons/recommended_products/recommends.tpl'}
{/if*}

{*include file='addons/accessories/product_recommended_list.tpl'*}

</div>
