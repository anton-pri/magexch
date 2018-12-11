{capture name=section}
<div class="product_modify block transparent">
<script type="text/javascript">
<!--
window.name="prodmodwin";
-->
</script>
{include file='main/products/product/geid_list.tpl'}

{jstabs name='product_data'}
default_tab={$js_tab|default:"product_details"}
default_template='main/products/product/modify.tpl'

[main]
title={$lng.lbl_product_details}
template="main/products/product/details_tabs.tpl"

[attributes]
title="{$lng.lbl_attributes}"
template="main/attributes/object_modify_product_attributes.tpl"

{if $product_id}
{if $addons.wholesale_trading}
[wholesale]
title="{$lng.lbl_wholesale_prices}"
template="addons/wholesale_trading/product_wholesale.tpl"
{/if}

{if $addons.magnifier && $usertype eq 'A'}
[zoomer]
title="{$lng.lbl_zoom_images}"
template="addons/magnifier/product_magnifier_modify.tpl"
{/if}

{if $addons.estore_products_review && $usertype eq 'A'}
[reviews]
title="{$lng.lbl_customer_reviews}"
template="addons/estore_products_review/admin_reviews_management.tpl"
{/if}

{if $addons.sn}
[serial_numbers]
title="{$lng.lbl_serial_numbers}"
template="main/products/product/product_serial_numbers.tpl"
{/if}

{if $addons.barcode}
[barcode]
title="{$lng.lbl_bar_codes}"
template="addons/barcode/print_preparation.tpl"
{/if}

[clients]
title="{$lng.lbl_clients}"

{/if}
{/jstabs}

{*include file='common/page_title.tpl' title=$product.product*}
{include file='admin/tabs/js_tabs.tpl'}
</div>
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$product.product}
