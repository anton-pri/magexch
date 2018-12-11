<script type="text/javascript">
<!--
window.name="prodmodwin";
-->
</script>
{include file='main/products/product/geid_list.tpl'}

{jstabs}
default_tab={$js_tab|default:'product_details'}
default_template="main/products/product/modify.tpl"

[main]
title={$lng.lbl_product_details}
template="main/products/product/details_tabs.tpl"

{if $addons.sn}
[serial_numbers]
title="{$lng.lbl_serial_numbers}"
template="main/products/product/product_serial_numbers.tpl"
{/if}

[modify_avails]
title="{$lng.lbl_modify_avails}"
template="main/products/product/modify_avails.tpl"

{/jstabs}

{include file='common/page_title.tpl' title=$product.product}
{include file='tabs/js_tabs.tpl'}
