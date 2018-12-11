{*if $docs_type eq 'I'}{include file='common/page_title.tpl' title=$lng.lbl_payment_quotes}{else}{include file='common/page_title.tpl' title=$lng.lbl_orders}{/if*}
{jstabs}
default_tab={$js_tab|default:"search_orders"}
default_template=main/orders/search.tpl

[submit]
title="{$lng.lbl_search}"
href="javascript: cw_submit_form(document.search_form);"

[reset]
title="{$lng.lbl_reset}"
href="javascript: cw_submit_form(document.search_form, 'reset');"

{* disabled add new order button due incorrect work of the feature: assigned wrong doc_display_id
{if $current_area eq 'A'}
[add]
title='{$lng.lbl_add_new}'
type='button'
href='index.php?target=docs_O&action=add'
{/if}
*}

[search_orders]
title="{lng name="lbl_doc_info_`$docs_type`"}"
display=true

[search_orders_advanced]
title={$lng.lbl_search_orders_advanced}

{/jstabs}
{capture name=section}

<form name="search_form" action="index.php?target={$current_target}" method="post">
<input type="hidden" name="action" value="search" />
<input type="hidden" name="js_tab" id="form_js_tab" value="">
{include file='tabs/search_tabs.tpl' is_checkboxes=1 name="search_sections" value=$search_prefilled.search_sections}
</form>
{/capture}
{if $docs_type eq 'I'}{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_payment_quotes}{else}{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_orders}{/if}


{if $mode eq 'search'}
{include file='common/navigation_counter.tpl'}

{if $orders}
{capture name=section2}
{include file='common/navigation.tpl'}
{include file='main/orders/orders_list.tpl'}
{include file='common/navigation.tpl'}
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section2 extra='width="100%"' title=$lng.lbl_search_results}
{/if}
{/if}


