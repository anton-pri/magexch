{jstabs}
default_tab={$js_tab|default:"search_orders"}
default_template=main/orders/search.tpl

[submit]
title="{$lng.lbl_search}"
href="javascript: cw_submit_form(document.search_form);"


{if $current_area eq 'A'}
[add]
title='{$lng.lbl_add_new}'
type='button'
href='index.php?target=docs_O&action=add'
{/if}

[search_orders]
title="{lng name="lbl_orders_step_1"}"
display=true

[search_orders_advanced]
title={$lng.lbl_search_orders_advanced}

{/jstabs}
<div style="display:none;">
{capture name=section}
<form name="search_form" action="index.php?target={$current_target}" method="post">
<input type="hidden" name="action" value="search" />
<input type="hidden" name="js_tab" id="form_js_tab" value="">
{include file='tabs/search_tabs.tpl' is_checkboxes=1 name="search_sections" value=$search_prefilled.search_sections}
</form>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_search_orders_menu}
</div>

{if $mode eq 'search'}

  {capture name=section2}
    {if $orders}
      {capture assign='undertitle_orders_list'}<div style="float: left">{$lng.lbl_customer_orders_list}</div><div style="float:right">{include file='common/navigation_counter.tpl'}</div>{/capture}
      {include file='main/orders/orders_list.tpl'}
      {include file='customer/orders_navigation.tpl'}
    {else}
      {assign var='undertitle_orders_list' value=$lng.lbl_you_have_not_placed_any_orders_yet}
    {/if}
  {/capture}
  {include file='customer/wrappers/jablock.tpl' is_dialog=0 content=$smarty.capture.section2 title=$lng.lbl_select_order undertitle_text=$undertitle_orders_list}

{/if}


