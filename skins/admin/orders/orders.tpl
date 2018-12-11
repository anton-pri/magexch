{*if $docs_type eq 'I'}{include file='common/page_title.tpl' title=$lng.lbl_payment_quotes}{else}{include file='common/page_title.tpl' title=$lng.lbl_orders}{/if*}
{capture name=section}

{jstabs}
default_tab={$js_tab|default:"search_orders"}
default_template=admin/orders/search.tpl

[submit]
title="{$lng.lbl_search}"
href="javascript: cw_submit_form(document.search_form);"
style="btn-green push-5-r"

[reset]
title="{$lng.lbl_reset}"
href="javascript: cw_submit_form(document.search_form, 'reset');"
style="btn-danger push-5-r"

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

<form name="search_form" action="index.php?target={$current_target}" method="post">
<input type="hidden" name="action" value="search" />
<input type="hidden" name="js_tab" id="form_js_tab" value="">
{include file='tabs/search_tabs.tpl' is_checkboxes=1 name="search_sections" value=$search_prefilled.search_sections}
</form>


{if $mode eq 'search'}
  {capture name=block}
      <div class="row">
        <div class="col-sm-6">{if $orders}{include file='common/per_page.tpl'}{/if}</div>
        <div class="col-sm-6">{include file='common/navigation_counter.tpl'}</div>
      </div>

      {if $orders}
      <div class="row">
        <div class="col-sm-12">
        {include file='admin/orders/orders_list.tpl'}
        </div>
      </div>
      {/if}
      <div class="row">
<div class="col-sm-6">{if $orders}{include file='common/per_page.tpl'}{/if}</div>
        <div class="col-sm-6">{include file='common/navigation.tpl'}</div>
      </div>
  {/capture}
  {include file="admin/wrappers/block.tpl" content=$smarty.capture.block title=$lng.lbl_search_results}
{/if}

{/capture}
{if $docs_type eq 'I'}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_payment_quotes}
{else}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_orders local_config='order'}
{/if}

