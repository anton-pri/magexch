{if $product_recommended eq ''}{tunnel func='cw_ac_get_recommended_smarty' via='cw_call' param1=$product.product_id assign='product_recommended'}{/if}
{if $product_recommended}
  {include file="addons/accessories/products_list.tpl"
    products=$product_recommended
    product_list_name="product_recommended"
    columns = $config.accessories.ac_rec_display_columns|default:1
    config_display_thumbnail=$config.accessories.ac_rec_display_thumbnail
    config_display_productcode=$config.accessories.ac_rec_display_productcode
    config_display_weight=$config.accessories.ac_rec_display_weight
    config_display_options=$config.accessories.ac_rec_display_options
    config_display_qty_in_stock=$config.accessories.ac_rec_display_qty_in_stock
    config_display_qty_selector=$config.accessories.ac_rec_display_qty_selector
    config_display_price=$config.accessories.ac_rec_display_price
    config_display_wholesale=$config.accessories.ac_rec_display_wholesale
    config_display_manufacturer=$config.accessories.ac_rec_display_manufacturer}
{else}
  {*<p>{$lng.txt_ac_there_are_no_recommended_products}</p>*}
{/if}
