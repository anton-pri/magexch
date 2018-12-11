{tunnel func='magexch_get_attribute_value' via='cw_call' param1='P' param2=$product.product_id param3='magexch_product_external_url' assign='product_external_url'}
{tunnel func='magexch_get_attribute_value' via='cw_call' param1='P' param2=$product.product_id param3='magexch_product_NUMBER_PAGES' assign='magexch_product_NUMBER_PAGES'}

{if $included_tab eq '1'}
{* start *}
  <div id="product-image-pos"></div>
  <div class="ProductData">
  <div class="product_border">
	{cms service_code="print_edition_tab"}
    <div class="learn-more"><a href="#">{$lng.lbl_learn_more}</a></div>
  </div>
  </div>
  <div class="ProductInfo">
    <span class="ProductDef">{$lng.lbl_details_of_magazine}:</span>
    <ul>
      <li>{$lng.lbl_number_of_pages}<span style="width: 20px;height: 15px;display:inline-block;float:right;"></span><span class="right">{if ($product.weight ne "0.00" || $variants ne '')}{$magexch_product_NUMBER_PAGES}   {/if}</span></li>

      <li>{$lng.lbl_shipping_weight_kg}<span style="width: 20px;height: 15px;display:inline-block;float:right;"></span><span class="right">{if ($product.weight ne "0.00" || $variants ne '')}{$product.weight|formatprice}   {/if}</span></li>
      <li>{$lng.lbl_shipping_cost}<span class="right question">{cms service_code="feature_not_activated" preload_popup="Y"}</span></li>
    </ul>
    <span class="ProductDef">{$lng.lbl_contents_listing}:</span> {$lng.lbl_see_below}
    <div class="wish_wrapper">{include file='buttons/add_to_wishlist.tpl'  href="javascript: if (FormValidation()) cw_submit_form('order_form', 'add2wl');"}<span class="right question">{cms service_code="wanted_popup" preload_popup="Y"}</span></div>
    <div class="sell_wrapper"><a href="" class="ProductBlue">{$lng.lbl_sell_this_item}</a><span class="right question">{cms service_code="sell_popup" preload_popup="Y"}</span></div>
  </div>

  <div class="clear"></div>

  <div class="sell_table">
{*
    <div class="sell_titles">
      <div class="price_cell">{$lng.lbl_price}</div>
      <div class="condition_cell">{$lng.lbl_condition}</div>
      <div class="descr_cell">{$lng.lbl_seller_descr}</div>
      <div class="seller_cell">{$lng.lbl_seller_info}</div>
      <div class="ready_cell">{$lng.lbl_ready_to_buy}</div>

    </div>

    <div class="sell_content">
      <div class="no_sellers">{$lng.lbl_currently_no_sellers}</div>
    </div>
*}
{assign var="is_non_digital_sellers_data" value=0}
{foreach from=$sellers_data item=mag_seller}{if !$mag_seller.is_digital && $mag_seller.quantity>0}{assign var="is_non_digital_sellers_data" value=1}{/if}{/foreach}

{if $is_non_digital_sellers_data}
    {foreach from=$sellers_data item=mag_seller}
    {if !$mag_seller.is_digital && $mag_seller.quantity>0}
    {tunnel func='cw_seller_get_info' via='cw_call' assign='seller_info' param1=$mag_seller.seller_id}
 
  	<div class="seller_block">
    	<ul>
      		<li><b>{$lng.lbl_seller_info_mobi}:</b> {*<span class="SellerName"><a title="All Magazines" href="index.php?cat={$config.custom_magazineexchange.magexch_default_root_category}&vendorid={$seller_info.customer_id}" style="color:blue">{$seller_info.name}</a></span>*}
                    {include file="main/seller_info.tpl" seller_customer_id=$mag_seller.seller_id}
                </li>
      	  	<li><b>{$lng.lbl_condition}:</b>  {tunnel func='cw_get_langvar_by_name' via='cw_call' param1="lbl_mag_seller_product_condition_`$mag_seller.condition`" param2='' param3=false param4=true}</li>
      		<li><b>{$lng.lbl_seller_descr_mobi}:</b> {$mag_seller.comments|nl2br}</li>
      		<li><b>{$lng.lbl_price}:</b> <span style="WHITE-SPACE: nowrap">{include file='common/currency.tpl' value=$mag_seller.price plain_text_message=true}</span></li>
    	</ul>
    	<div class="seller_add_to_cart">
            {*<div class="hidden_qty">{include file='customer/products/product-amount.tpl'}</div>*}

        	{include file='buttons/add_to_cart.tpl' button_title=$lng.lbl_add_to_cart style='btn' href="javascript: document.order_form.attributes.action.value += '&seller_item_id=`$mag_seller.seller_item_id`'; cw_submit_form('order_form');"} 
    	</div>
  	</div>
  	
    {/if}
    {/foreach}
{else}
  <div class="seller_block">
      <strong>{$lng.lbl_currently_no_sellers}</strong>
  </div>
{/if}  	
  </div>

{elseif $included_tab eq 2}
{* start *}
  <div id="product-image-pos1"></div>
  <div class="ProductData">
  <div class="product_border">
    <img src="{$AltImagesDir}/marketplace.png" width="137" height="25">
	{cms service_code="digital_edition_tab"}
    <div class="learn-more"><a href="#">{$lng.lbl_learn_more}</a></div>
  </div>
  </div>
  <div class="ProductInfo">
    <span class="ProductDef">{$lng.lbl_details_of_magazine}:</span>
    <ul>
      <li>{$lng.lbl_number_of_pages}<span style="width: 20px;height: 15px;display:inline-block;float:right;"></span><span class="right">{if ($product.weight ne "0.00" || $variants ne '')}{$magexch_product_NUMBER_PAGES}   {/if}</span></li>
      <li>{$lng.lbl_shipping_weight_kg}<span style="width: 20px;height: 15px;display:inline-block;float:right;"></span><span class="right">0</span></li>
      <li>{$lng.lbl_shipping_cost} <span class="right question">{cms service_code="feature_not_activated" preload_popup="Y"}</span></li>
    </ul>
    <span class="ProductDef">{$lng.lbl_digital_editions_feedback}:</span> 
      <ul>
        <li class="adb_text">{cms service_code='product_banner'}</li> 
      </ul>
    <div class="sell_wrapper" style="margin-top: 3px"><a href="" class="ProductBlue">{$lng.lbl_sell_this_item}</a><span class="right question">{cms service_code="sell_popup" preload_popup="Y"}</span></div>
  </div>

  <div class="clear"></div>
  
<!-- External Digital Products -->
  {if $external_links}
  <h4>Digital editions from other Retailers</h4>

  {foreach from=$external_links item=link}
  <div class="seller_block">
    <ul>
      <li><b>{$lng.lbl_seller_info}:</b> <span class="SellerName">{$link.seller}</span></li>
      <li><b>{$lng.lbl_seller_descr}:</b> {$link.format}</li>
      <li><b>{$lng.lbl_condition}:</b> {$link.comment|escape}</li>
      <li><b>{$lng.lbl_price}:</b> <span style="WHITE-SPACE: nowrap">{include file='common/currency.tpl' value=$link.price plain_text_message=true}</span></li>
    </ul>
    <div class="seller_add_to_cart">
		<a target='_blank' href='{$link.link}' onclick="_gaq.push(['_trackEvent','{$link.category}','{$link.action}','{$link.value}'])"><img src="{$AltImagesDir}/external_link_button.gif" alt="" style="margin-top: 7px;" /></a>
    </div>
  </div>
  {/foreach}
  {else}
  {/if}

<!-- / External Digital Products -->
  {include file="customer/products/seller_info_digital.tpl"}

{/if}
