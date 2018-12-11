{jstabs}
default_tab={$js_tab|default:"products"}

[products]
title="{$lng.lbl_products}"
template="addons/advanced_order_management/edit_products.tpl"

[products_search]
title="{$lng.lbl_search_products}"
template="addons/advanced_order_management/search_products.tpl"

[customer]
title="{$lng.lbl_invoice}"
template="addons/advanced_order_management/edit_customer.tpl"
{/jstabs}
<div class="top_error" id="ajax_errors"></div>
{include file='tabs/js_tabs.tpl' is_pos=true}
{if !$js_tab or $js_tab eq 'products'}
<script language="javascript">
document.getElementById('ean_ean_0').focus();
</script>
{/if}
{if $config.pos.pos_reload ne 'Y' || true}
<script language="javascript">
shortcut("F5",function() {ldelim}
    window.href.location = 'index.php?target={$current_target}&action=add';
{rdelim});
</script>
{/if}
