
  <div class="sell_table">
{$lng.txt_digital_editions_magex}
{assign var="is_digital_sellers_data" value=0}
{foreach from=$sellers_data item=mag_seller}{if $mag_seller.is_digital}{assign var="is_digital_sellers_data" value=1}{/if}{/foreach}

{if $is_digital_sellers_data}
           <input type="text" name="x1" value="0" id="x1" hidden>
           <input type="text" name="x2" value="0" id="x2" hidden>
           <input type="text" name="y1" value="0" id="y1" hidden>
           <input type="text" name="y2" value="0" id="y2" hidden>
           <div id="free_preview_dialog"></div>
{/if}

{*$sellers_data|@debug_print_var*}
  <table border="0" cellspacing="0" cellpadding="2" width="100%" class="SellerTable">
    <tbody>
      <tr class="ProductTableHead">
	  <td align="center" width="90" style="width: 90px;">{$lng.lbl_price}</td>
	  <td align="center" width="90" style="width: 90px;">{$lng.lbl_digital_format|default:'Digital Format'}</td>
	  <td align="center" width="130" style="width: 130px;">{$lng.lbl_seller}</td>

	  <td align="center" width="100" style="width: 100px;">{$lng.lbl_free_preview}</td>
	  <td align="center" width="100" style="width: 100px;">Comments</td>
	  <td align="center" width="295" style="width: 295px;">{$lng.lbl_ready_to_buy}</td>
      </tr>

{if $is_digital_sellers_data}
    {foreach from=$sellers_data item=mag_seller}
    {if $mag_seller.is_digital}
      <tr>
	  <td align="center"><span class="seller_price">{include file='common/currency.tpl' value=$mag_seller.price plain_text_message=true}</span></td>
	  <td align="center">{tunnel func='magexch_get_attribute_value' via='cw_call' param1='SP' param2=$mag_seller.seller_item_id param3='seller_product_file_type' assign='seller_product_file_type'}{$seller_product_file_type}</td>
	  <td align="center">
          {tunnel func='cw_seller_get_info' via='cw_call' assign='seller_info' param1=$mag_seller.seller_id}
          <span class="SellerName"><a title="All Magazines" href="index.php?cat={$config.custom_magazineexchange.magexch_default_root_category}&vendorid={$seller_info.customer_id}" style="color:blue">{$seller_info.name}</a></span>
      </td>

	  <td align="center">
{tunnel func='magexch_get_attribute_value' via='cw_call' param1='SP' param2=$mag_seller.seller_item_id param3='seller_product_preview_file' assign='seller_product_preview_file'}
{tunnel func='cw_ppd_as3_real_url' via='cw_call' param1=$seller_product_preview_file assign='seller_product_preview_file'}
{if $seller_product_preview_file ne ''}
           <a onclick="return show_free_preview_dialog('{$seller_product_preview_file}')"><img src="{$AltImagesDir}/free_preview.png" /></a>
{/if}
      </td>
	  <td align="center">
         {$mag_seller.comments|nl2br}
      </td>
	  <td align="center" class="buy_button">
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
      </td>
      </tr>
    {/if}
    {/foreach}
{else}
      <tr><td colspan="6" align="center"><strong>{$lng.lbl_currently_no_sellers}</strong></td></tr>
{/if}

    </tbody>
    </table>
  </div>
