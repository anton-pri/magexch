{jstabs}
default_tab={$js_tab|default:"customer"}

[customer]
title="{$lng.lbl_customer}"
template="addons/advanced_order_management/edit_customer.tpl"

[products]
title="{$lng.lbl_products}"
template="addons/advanced_order_management/edit_products.tpl"

[totals]
title="{$lng.lbl_totals}"
template="addons/advanced_order_management/edit_totals.tpl"

[preview]
title="{$lng.lbl_preview}"
template="addons/advanced_order_management/preview.tpl"

{/jstabs}

{if $confirmation eq "Y"}
{include file="addons/advanced_order_management/confirmation.tpl"}
{elseif $rejected}
{$lng.lbl_aom_edit_rejected}
{else}
{include file='tabs/js_tabs.tpl'}
{/if}