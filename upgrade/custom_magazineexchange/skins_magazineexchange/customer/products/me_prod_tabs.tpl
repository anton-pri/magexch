{*tunnel func='magexch_get_attribute_value' via='cw_call' param1='P' param2=$product.product_id param3='magexch_product_external_url' assign='product_external_url'*}
{tunnel func='magexch_get_attribute_value' via='cw_call' param1='P' param2=$product.product_id param3='magexch_product_NUMBER_PAGES' assign='magexch_product_NUMBER_PAGES'}

{if $included_tab eq '1'}
{* start *}
  <div id="product-image-pos"></div>
  <div class="ProductData">
    {include file="customer/products/buy_or_sell_copies.tpl"}  
  </div>
  <div class="ProductInfo">
    {include file="customer/products/details_of_magazine.tpl"}
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
  <table border="0" cellspacing="0" cellpadding="2" width="100%" class="SellerTable">
    <tbody>
      <tr class="ProductTableHead">
	  <td align="center" width="90">{$lng.lbl_price}</td>
	  <td align="center" width="120">{$lng.lbl_condition}</td>
	  <td align="center">{$lng.lbl_seller_descr}</td>
	  <td align="center" width="230">{$lng.lbl_seller_info}</td>
	  <td align="center" width="210">{$lng.lbl_ready_to_buy}</td>
      </tr>
{assign var="is_non_digital_sellers_data" value=0}
{foreach from=$sellers_data item=mag_seller}{if !$mag_seller.is_digital && $mag_seller.quantity>0}{assign var="is_non_digital_sellers_data" value=1}{/if}{/foreach}

{if $is_non_digital_sellers_data}
    {foreach from=$sellers_data item=mag_seller}
    {if !$mag_seller.is_digital && $mag_seller.quantity>0}
    {*tunnel func='cw_seller_get_info' via='cw_call' assign='seller_info' param1=$mag_seller.seller_id*}
    <tr>
    <td align="center" class="SellerPrice">
        <span style="WHITE-SPACE: nowrap">{include file='common/currency.tpl' value=$mag_seller.price plain_text_message=true}</span>
    </td>
    <td align="center">    {tunnel func='cw_get_langvar_by_name' via='cw_call' param1="lbl_mag_seller_product_condition_`$mag_seller.condition`" param2='' param3=false param4=true} </td>
    <td>{$mag_seller.comments|nl2br}</td>
    <td>
      {*<span class="SellerName"><a title="All Magazines" href="index.php?cat={$config.custom_magazineexchange.magexch_default_root_category}&vendorid={$seller_info.customer_id}" style="color:blue">{$seller_info.name}</a></span>*}
      {include file="main/seller_info.tpl" seller_customer_id=$mag_seller.seller_id}
    </td>
    <td align="center" class="buy_button">
        {include file='buttons/add_to_cart.tpl' button_title=$lng.lbl_add_to_cart style='btn' href="javascript: document.order_form.attributes.action.value += '&seller_item_id=`$mag_seller.seller_item_id`'; cw_submit_form('order_form');"} 
    </td>
    </tr>
    {/if}
    {/foreach}
{else}
      <tr><td colspan="5" align="center"><strong>{$lng.lbl_currently_no_sellers}</strong></td></tr>
{/if}

    </tbody>
    </table>
  </div>

{elseif $included_tab eq 2}
{* start *}
  <div id="product-image-pos1"></div>
  <div class="ProductData">
  <div class="product_border">
    <img src="{$AltImagesDir}/marketplace.png" width="137" height="25">
	{cms service_code="digital_edition_tab"}
    <div class="learn-more">{cms service_code="digital_editions_pop_up" preload_popup="Y"}</div>
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
    <div class="sell_wrapper" style="margin-top: 3px"><a href="/cw/seller/index.php" class="ProductBlue">{$lng.lbl_sell_this_item}</a><span class="right question">{cms service_code="sell_popup" preload_popup="Y"}</span></div>
  </div>

  <div class="clear"></div>
  
<!-- External Digital Products -->
    {include file="customer/products/external_links.tpl"}
<!-- / External Digital Products -->

  {include file="customer/products/seller_info_digital.tpl"}

{/if}
