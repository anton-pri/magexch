{assign var='current_tab' value=1}

{jstabs name='product_data_customer'}
default_tab={$current_tab}
default_template="customer/products/me_prod_tabs.tpl"

[1]
title="Custom skin tab title"

{/jstabs}
{include file='tabs/js_tabs.tpl' tab_extra_cls="style='width:100%'" }
