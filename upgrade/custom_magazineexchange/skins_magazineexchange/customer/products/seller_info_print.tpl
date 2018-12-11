
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
{if $sellers_data}
    {foreach from=$sellers_data item=mag_seller}
    {tunnel func='cw_seller_get_info' via='cw_call' assign='seller_info' param1=$mag_seller.seller_id}
    <tr>
    <td align="center" class="SellerPrice">
        <span style="WHITE-SPACE: nowrap">{include file='common/currency.tpl' value=$mag_seller.price plain_text_message=true}</span>
    </td>
    <td align="center">    {tunnel func='cw_get_langvar_by_name' via='cw_call' param1="lbl_mag_seller_product_condition_`$mag_seller.condition`" param2='' param3=false param4=true} </td>
    <td>{$mag_seller.comments}</td>
    <td><span class="SellerName"><a title="All Magazines" href="index.php?cat={$config.custom_magazineexchange.magexch_default_root_category}&vendorid={$seller_info.customer_id}" style="color:blue">{$seller_info.name}</a></span></td>
    <td align="center" class="buy_button">
        {include file='buttons/add_to_cart.tpl' button_title=$lng.lbl_add_to_cart style='btn' href="javascript: document.order_form.attributes.action.value += '&seller_id=`$mag_seller.seller_id`'; cw_submit_form('order_form');"} 
    </td>
    </tr>

    {/foreach}
{else}
      <tr><td colspan="5" align="center"><strong>{$lng.lbl_currently_no_sellers}</strong></td></tr>
{/if}

    </tbody>
    </table>
  </div>

