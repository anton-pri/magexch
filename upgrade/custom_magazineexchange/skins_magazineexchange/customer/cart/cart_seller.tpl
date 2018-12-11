    <div class="CartSellerData">
    <table border="0" cellspacing="0" cellpadding="2" width="100%" class="SellerTable">
    <tbody>
       <tr class="ProductTableHead">
      <td align="center" width="120">Condition</td>
      <td align="center">Seller's Description</td>
      <td align="center" width="170">Seller Information</td>
       </tr>
    {tunnel func='cw_seller_get_info' via='cw_call' assign='seller_info' param1=$product.seller_id}
    {tunnel func='cw\custom_magazineexchange_sellers\mag_product_seller_data' via='cw_call' assign='seller_data' param1=$product.product_id param2=$product.seller_id}
       <tr>
      <td align="center">{tunnel func='cw_get_langvar_by_name' via='cw_call' param1="lbl_mag_seller_product_condition_`$seller_data.condition`" param2='' param3=false param4=true}</td>

      <td align="center">{$seller_data.comments}</td>
      <td align="center"><span class="SellerName">{$seller_info.name}</span><br></td>
      </tr>
    </tbody>
    </table>
    </div>
