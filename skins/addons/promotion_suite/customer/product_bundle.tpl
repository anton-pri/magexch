{capture name=title}
Buy together and get discount
<span style='color: red;'>
{if $product_bundle.bonuses.D.disctype eq 1}
    {include file='common/currency.tpl' value=$product_bundle.bonuses.D.discount}
{else}
{$product_bundle.bonuses.D.discount}%
{/if}
</span> 
{/capture}
{*include file='customer/products/products_gallery.tpl' products=$product_bundle.products*}
  {include file="addons/accessories/products_list.tpl"
    products=$product_bundle.products
    product_list_name="product_accessories"
    columns = $config.accessories.ac_acc_display_columns|default:1
    config_display_thumbnail=$config.accessories.ac_acc_display_thumbnail
    config_display_productcode=$config.accessories.ac_acc_display_productcode
    config_display_weight=$config.accessories.ac_acc_display_weight
    config_display_options=false
    config_display_qty_in_stock=false
    config_display_qty_selector=false
    config_display_price=$config.accessories.ac_acc_display_price
    config_display_wholesale=false
    config_display_manufacturer=$config.accessories.ac_acc_display_manufacturer
    title=$smarty.capture.title}
