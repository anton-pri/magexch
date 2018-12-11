{assign var="is_digital_sellers_data" value=0}
{foreach from=$sellers_data item=mag_seller}{if $mag_seller.is_digital}{assign var="is_digital_sellers_data" value=1}{/if}{/foreach}

{*<div class="sell_table incomplete">
{if $product.avail eq 0}
  <div class="seller_block">
      <strong>{$lng.lbl_currently_no_sellers}</strong>
  </div>
{else}
  <div class="seller_block">
    <ul>
      <li><b>{$lng.lbl_seller_info}:</b> <span class="SellerName"><a title="All Magazines" href="#" style="color:blue">keyseller (10)</a></span></li>
      <li><b>{$lng.lbl_seller_descr}:</b> New stock direct from publisher</li>
      <li><b>{$lng.lbl_condition}:</b> New</li>
      <li><b>{$lng.lbl_price}:</b> <span style="WHITE-SPACE: nowrap">{include file='common/currency.tpl' value=$product.display_price plain_text_message=true}</span></li>
    </ul>
    <div class="seller_add_to_cart">
            <div class="hidden_qty">{include file='customer/products/product-amount.tpl'}</div>

            {include file='buttons/add_to_cart.tpl' button_title=$lng.lbl_add_to_basket style='btn' href="javascript: if(FormValidation()) cw_submit_form('order_form');"}
    </div>
  </div>
  <div class="seller_block">
    <ul>
      <li><b>{$lng.lbl_seller_info}:</b> <span class="SellerName"><a title="All Magazines" href="#" style="color:blue">keyseller (10)</a></span></li>
      <li><b>{$lng.lbl_seller_descr}:</b> New stock direct from publisher</li>
      <li><b>{$lng.lbl_condition}:</b> New</li>
      <li><b>{$lng.lbl_price}:</b> <span style="WHITE-SPACE: nowrap">{include file='common/currency.tpl' value=$product.display_price plain_text_message=true}</span></li>
    </ul>
    <div class="seller_add_to_cart">
            <div class="hidden_qty">{include file='customer/products/product-amount.tpl'}</div>

            {include file='buttons/add_to_cart.tpl' button_title=$lng.lbl_add_to_basket style='btn' href="javascript: if(FormValidation()) cw_submit_form('order_form');"}
    </div>
  </div>
{/if}	
</div>
*}
{if $is_digital_sellers_data}
    {foreach from=$sellers_data item=mag_seller}
    {if $mag_seller.is_digital}
    
      <div class="seller_block">
    	<ul>
    	{tunnel func='cw_seller_get_info' via='cw_call' assign='seller_info' param1=$mag_seller.seller_id}
      	<li><b>{$lng.lbl_seller_info}:</b> 
        <span class="SellerName"><a title="All Magazines" href="index.php?cat={$config.custom_magazineexchange.magexch_default_root_category}&vendorid={$seller_info.customer_id}" style="color:blue">{$seller_info.name}</a></span>
        </li>
      	<li><b>{$lng.lbl_seller_descr}:</b> {tunnel func='magexch_get_attribute_value' via='cw_call' param1='SP' param2=$mag_seller.seller_item_id param3='seller_product_file_type' assign='seller_product_file_type'}{$seller_product_file_type}</li>
      	<li><b>{$lng.lbl_condition}:</b>{$mag_seller.comments|nl2br}</li>
      	<li><b>{$lng.lbl_price}:</b> <span style="WHITE-SPACE: nowrap">{include file='common/currency.tpl' value=$mag_seller.price plain_text_message=true}</span></li>
    	</ul>
    	<div class="seller_add_to_cart">
		{tunnel func='cw\custom_magazineexchange_sellers\mag_get_seller_digital_product_sale' via='cw_call' param1=$mag_seller.seller_item_id param2=$customer_id assign='seller_product_sale'}
        {if $seller_product_sale.download_link}
          {if !$seller_product_sale.download_link_expired}
            <span class="SellerName"><a title="{$lng.lbl_download}" href="{$seller_product_sale.download_link}" style="color:blue" target="_blank">{$lng.lbl_download}</a></span>
          {else}
            {$lng.lbl_download_link_expired|default:'Download link expired'}
          {/if}
        {else}
          {tunnel func='cw\custom_magazineexchange_sellers\mag_check_digital_seller_product_in_cart' via='cw_call' param1=$mag_seller.seller_item_id assign='is_digital_product_in_cart'}
            <span id="already_in_basket_{$mag_seller.seller_item_id}"{if !$is_digital_product_in_cart} style="display:none"{/if}>{$lng.lbl_already_in_cart|default:'Already in basket'}</span>
            <div id="add2basket_{$mag_seller.seller_item_id}" {if $is_digital_product_in_cart}style="display:none"{/if}>
            {include file='buttons/add_to_cart.tpl' button_title=$lng.lbl_add_to_cart style='btn' href="javascript: document.order_form.attributes.action.value += '&seller_item_id=`$mag_seller.seller_item_id`'; cw_submit_form('order_form');"}
            </div>
        {/if}
    	</div>
  	  </div>
    {/if}
    {/foreach}
{else}
  <div class="seller_block">
      <strong>{$lng.lbl_currently_no_sellers}</strong>
  </div>
{/if}  	  
