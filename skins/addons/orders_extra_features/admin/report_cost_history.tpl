{*include file='common/page_title.tpl' title=$lng.lbl_profits_cost_history*}
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

[search_orders]
title="{lng name="lbl_doc_info_`$docs_type`"}"
display=always

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
        <div class="col-sm-6">{include file='common/per_page.tpl'}</div>
        <div class="col-sm-6">{include file='common/navigation_counter.tpl'}</div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          {include file='addons/orders_extra_features/admin/report_cost_history_list.tpl'}
        </div>
      </div>
      <div class="row">
        <div class="col-sm-6">{include file='common/per_page.tpl'}</div>
        <div class="col-sm-6">{include file='common/navigation.tpl'}</div>
      </div>
  {/capture}
  {include file="admin/wrappers/block.tpl" content=$smarty.capture.block title=$lng.lbl_profits_cost_history}
{/if}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_profits_cost_history}
