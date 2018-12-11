{*if $smarty.get.target eq 'docs_I'}{include file='common/page_title.tpl' title=$lng.lbl_create_quote}{else}{include file='common/page_title.tpl' title=$lng.lbl_order_info}{/if*}
{capture name=section}
<div class="block">
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

{* kornev, disabled for now*}
{* [products_search]
title="{$lng.lbl_search_products}"
template="addons/advanced_order_management/search_products.tpl" *}

[totals]
title="{$lng.lbl_details}"
{if $order.type eq 'D'}
template="addons/advanced_order_management/edit_totals_D.tpl"
{else}
template="addons/advanced_order_management/edit_totals.tpl"
{/if}

[preview]
title="{$lng.lbl_preview}"
template="addons/advanced_order_management/preview.tpl"

{/jstabs}

{if $confirmation eq "Y"}
{include file="addons/advanced_order_management/confirmation.tpl"}
{elseif $rejected}
{$lng.lbl_aom_edit_rejected}
{else}
<div class="top_error" id="ajax_errors"></div>
{include file='admin/tabs/js_tabs.tpl'}
{/if}

</div>
{/capture}
{if $smarty.get.target eq 'docs_I'}{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_create_quote}{else}{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_order_info}{/if}
