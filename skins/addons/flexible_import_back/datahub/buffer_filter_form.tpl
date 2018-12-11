{if $buffer_search.map eq 'blacklist'}{assign var='is_blacklist_display' value=true}{/if}
<form name="buffer_filter_form" method="post" action="index.php?target=datahub_buffer_match">
  <input type="hidden" name="action" value="apply_filter" />
    {if $current_target eq 'datahub_buffer_match_edit'}<input type="hidden" name="is_edit" value="Y" />{/if}
          <input type="radio" id="adv_filter_map_all" name="adv_filter[map]" value="" {if $buffer_search.map eq ''}checked="checked"{/if}/><label for="adv_filter_map_all" title="Except blacklist">&nbsp;All*</label>&nbsp;&nbsp;
          <input type="radio" id="adv_filter_map_not_mapped" name="adv_filter[map]" value="not_mapped" {if $buffer_search.map eq 'not_mapped'}checked="checked"{/if}/><label for="adv_filter_map_not_mapped">&nbsp;Not Mapped</label>&nbsp;&nbsp;
          <input type="radio" id="adv_filter_map_mapped" name="adv_filter[map]" value="mapped" {if $buffer_search.map eq 'mapped'}checked="checked"{/if}/><label for="adv_filter_map_mapped">&nbsp;Mapped</label>&nbsp;&nbsp;
          <input type="radio" id="adv_filter_map_new" name="adv_filter[map]" value="new" {if $buffer_search.map eq 'new'}checked="checked"{/if}/><label for="adv_filter_map_new">&nbsp;New</label>&nbsp;&nbsp;
          <input type="radio" id="adv_filter_map_blacklist" name="adv_filter[map]" value="blacklist" {if $buffer_search.map eq 'blacklist'}checked="checked"{/if}/><label for="adv_filter_map_blacklist">&nbsp;Blacklist</label>&nbsp;&nbsp;
        
          <input type="text" style="width:30%; margin-right:20px;" name="adv_filter[text_search]" value="{$buffer_search.text_search}"/>

      {include file='admin/buttons/button.tpl' button_title=$lng.lbl_apply_filter|default:'Apply Filter' href="javascript: cw_submit_form('buffer_filter_form');" style='btn-green push-20 push-15-t'}
{if $current_target eq 'datahub_buffer_match' && !$is_blacklist_display}
&nbsp;&nbsp;|&nbsp;<a href="index.php?target=datahub_buffer_match_edit"><nobr>Edit Import Buffer Items</nobr></a>
{elseif $current_target eq 'datahub_buffer_match_edit'}
&nbsp;&nbsp;|&nbsp;<a href="index.php?target=datahub_buffer_match"><nobr>View Import Buffer List</nobr></a>
{/if}
</form>
