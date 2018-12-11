{jstabs}
default_tab={$js_tab|default:"search_orders"}
default_template=main/orders/search.tpl

[submit]
title="{$lng.lbl_search}"
href="javascript: cw_submit_form(document.search_form);"

[reset]
title="{$lng.lbl_reset}"
href="javascript: cw_submit_form(document.search_form, 'reset');"

[search_orders]
title="{lng name="lbl_doc_info_`$docs_type`"}"

[search_orders_advanced]
title={$lng.lbl_search_orders_advanced}

[search_orders_products]
title={$lng.lbl_search_orders_products}

[search_orders_customer]
title="{lng name="lbl_doc_customer_`$docs_type`"}"

[cause]
title="{$lng.lbl_causale}"

{/jstabs}

<form name="search_form" action="index.php?target={$current_target}" method="post">
<input type="hidden" name="action" value="search" />
<input type="hidden" name="js_tab" id="form_js_tab" value="">
{include file='tabs/js_tabs.tpl' is_checkboxes=1 name="search_sections" value=$search_prefilled.search_sections}
</form>

{if $mode eq 'search'}
{include file='common/navigation_counter.tpl'}
{include file='common/navigation.tpl'}
{include file='main/docs/popup_orders_list.tpl'}
{include file='common/navigation.tpl'}
{/if}
