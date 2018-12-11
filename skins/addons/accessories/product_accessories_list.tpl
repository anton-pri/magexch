{if $product_accessories}
  {include file="addons/accessories/products_list.tpl"
    products=$product_accessories
    product_list_name="product_accessories"
    columns = $config.accessories.ac_acc_display_columns|default:1
    config_display_thumbnail=$config.accessories.ac_acc_display_thumbnail
    config_display_productcode=$config.accessories.ac_acc_display_productcode
    config_display_weight=$config.accessories.ac_acc_display_weight
    config_display_options=$config.accessories.ac_acc_display_options
    config_display_qty_in_stock=$config.accessories.ac_acc_display_qty_in_stock
    config_display_qty_selector=$config.accessories.ac_acc_display_qty_selector
    config_display_price=$config.accessories.ac_acc_display_price
    config_display_wholesale=$config.accessories.ac_acc_display_wholesale
    config_display_manufacturer=$config.accessories.ac_acc_display_manufacturer}
{else}
  <p>{$lng.txt_ac_there_are_no_product_accessories}</p>
{/if}
