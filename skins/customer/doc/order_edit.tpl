{jstabs}
default_tab={$js_tab|default:"customer"}

{if $order.type ne 'D'}
[customer]
{if $order.type eq 'P'}
title="{$lng.lbl_supplier}"
{else}
title="{$lng.lbl_customer}"
{/if}
template="addons/advanced_order_management/edit_customer.tpl"
{/if}

[products]
title="{$lng.lbl_products}"
template="addons/advanced_order_management/edit_products.tpl"

[products_search]
title="{$lng.lbl_search_products}"
template="addons/advanced_order_management/search_products.tpl"

[preview]
title="{$lng.lbl_preview}"
template="addons/advanced_order_management/preview.tpl"

{/jstabs}

{if $confirmation eq "Y"}
{include file='addons/advanced_order_management/confirmation_customer.tpl'}
{elseif $rejected}
{$lng.lbl_aom_edit_rejected}
{else}
<div class="top_error" id="ajax_errors"></div>
{include file='tabs/js_tabs.tpl'}
{/if}
