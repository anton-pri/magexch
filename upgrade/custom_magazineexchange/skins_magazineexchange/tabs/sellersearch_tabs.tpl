{include_once file='tabs/search_tabs_js.tpl'}

{capture name=block}
    <div class="form-horizontal">

     <!-- <h3 class="block-title push-15">{$lng.lbl_additional_criteria}</h3> -->



 <div class="form-group" id='active_sections'>
        <div class="col-xs-12">
        {foreach from=$js_tabs item=tab key=ct}
           
             {assign var=tab_id value="tab_`$ct`"}
             {if !$tab.display}
               <div class="sellersearchcheckbox"><label><input type='checkbox' value='1' name='{$name}[tab_{$ct}]' id='{$ct}' {if $value.$tab_id} checked="checked"{/if} /> {$tab.title}</label></div>
             {else}
               <input type='hidden' value='1' name='{$name}[tab_{$ct}]' id='{$ct}' checked="checked" />
             {/if}
           
        {/foreach}
        </div>















      </div>
	</div>
  {foreach from=$js_tabs item=tab key=ct}
  <div class='search_tabs_section' id="{$ct}_section" style="display: none;">
     <!-- <h3 class="block-title push-15">{$tab.title}</h3>-->
     <!-- {$tab.template}-->
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
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}