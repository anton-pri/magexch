{include_once file='tabs/search_tabs_js.tpl'}

{capture name=block}
    <div class="form-horizontal">

      <h3 class="block-title push-15">{$lng.lbl_additional_criteria}</h3>

      <div class="form-group" id='active_sections'>
        <div class="col-xs-12">
        {foreach from=$js_tabs item=tab key=ct}
           
             {assign var=tab_id value="tab_`$ct`"}
             {if !$tab.display}
               <div class="checkbox"><label><input type='checkbox' value='1' name='{$name}[tab_{$ct}]' id='{$ct}' {if $value.$tab_id} checked="checked"{/if} /> {$tab.title}</label></div>
             {else}
               <input type='hidden' value='1' name='{$name}[tab_{$ct}]' id='{$ct}' checked="checked" />
             {/if}
           
        {/foreach}
        </div>
      </div>
	</div>
  {foreach from=$js_tabs item=tab key=ct}
  <div class='search_tabs_section' id="{$ct}_section" style="display: none;">
      <h3 class="block-title push-15">{$tab.title}</h3>
    {include file=$tab.template included_tab=$ct}
  </div>
  {/foreach}

    <div class="form-horizontal">

      {if $js_tab_buttons}
        <div class="form-group">
        <div class="col-xs-12">
        {foreach from=$js_tab_buttons item=button}
          {include file='admin/buttons/button.tpl' button_title=$button.title href=$button.href style=$button.style}
        {/foreach}
        </div>
        </div>
      {/if}

    </div>

{if $current_area eq 'A' && ($current_target eq 'products' || $current_target eq 'docs_O')}
<div class="form-horizontal">
<div class="form-group">
	<div class="col-xs-6"><input type='text' class="form-control" name='save_search_name' id='save_search_name' value="{$current_loaded_search_name}" placeholder="{$lng.lbl_saved_search_name|default:'Saved search name'}" /></div>
	<div class="col-xs-6">{include file='admin/buttons/button.tpl' button_title=$lng.lbl_save_search|default:'Save search' href="javascript: cw_submit_form('search_form', 'save_search');" style="btn-green"}</div>
</div>
<div class="form-group">
	<div class="col-xs-6">
	  <select class="form-control" name="save_search_restore" title="{$lng.lbl_load_saved_search|default:"Load saved search"}">
		<option value="">empty</option>
		{foreach from=$saved_searches item=ssi}
		<option value="{$ssi.ss_id}" {if $ssi.ss_id eq $current_loaded_search_id}selected="selected"{/if}>{$ssi.name|stripslashes}</option>
		{/foreach}
	  </select>
	</div>
	<div class="col-xs-6">
		{include file='admin/buttons/button.tpl' button_title=$lng.lbl_load|default:'Load' href="javascript: cw_submit_form('search_form', 'save_search_load');" style="btn-green"}
		{if $current_loaded_search_id gt 0}
		&nbsp;
		{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete href="javascript: cw_submit_form('search_form', 'delete_search_load');" style="btn-green"}
		&nbsp;
		{/if}
	</div>
</div>
</div>
{/if}

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
