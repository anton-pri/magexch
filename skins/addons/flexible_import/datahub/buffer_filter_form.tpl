{if $buffer_search.map eq 'blacklist'}{assign var='is_blacklist_display' value=true}{/if}
<form name="buffer_filter_form" method="post" action="index.php?target=datahub_{$interim_ext}buffer_match">
  <input type="hidden" name="action" value="apply_filter" />
    {if $current_target eq 'datahub_buffer_match_edit'}<input type="hidden" name="is_edit" value="Y" />{/if}

 {if $interim_ext eq '' || 1}
          <input type="radio" id="adv_filter_map_all" name="adv_filter[map]" value="" {if $buffer_search.map eq ''}checked="checked"{/if}/><label for="adv_filter_map_all" title="Except blacklist">&nbsp;All*</label>&nbsp;&nbsp;
          <input type="radio" id="adv_filter_map_not_mapped" name="adv_filter[map]" value="not_mapped" {if $buffer_search.map eq 'not_mapped'}checked="checked"{/if}/><label for="adv_filter_map_not_mapped">&nbsp;Not Mapped</label>&nbsp;&nbsp;
          <input type="radio" id="adv_filter_map_mapped" name="adv_filter[map]" value="mapped" {if $buffer_search.map eq 'mapped'}checked="checked"{/if}/><label for="adv_filter_map_mapped">&nbsp;Mapped</label>&nbsp;&nbsp;
{/if}

{if $interim_ext eq ''}
          <input type="radio" id="adv_filter_map_new" name="adv_filter[map]" value="new" {if $buffer_search.map eq 'new'}checked="checked"{/if}/><label for="adv_filter_map_new">&nbsp;New</label>&nbsp;&nbsp;
          <input type="radio" id="adv_filter_map_blacklist" name="adv_filter[map]" value="blacklist" {if $buffer_search.map eq 'blacklist'}checked="checked"{/if}/><label for="adv_filter_map_blacklist">&nbsp;Blacklist</label>&nbsp;&nbsp;
{/if}
        
    {if $interim_ext ne ''}
        {tunnel via='cw_call' func='cw_datahub_get_buffer_sources' param1=1 assign='buffer_sources'}  
          {if $buffer_sources ne ''}
          <select name="adv_filter[source]" title="Feed source">
          <option value="">Source: All</option>
{foreach from=$buffer_sources item=b}
          <option value="{$b.code}" {if $buffer_search.source eq $b.code}selected="selected"{/if}>{$b.name|default:$b.code}</option>  
{/foreach}
          </select>&nbsp;&nbsp;
          {/if}
    {/if}
      <input type="text" style="width:30%; margin-right:20px;" name="adv_filter[text_search]" value="{$buffer_search.text_search}"/>
      <span id="apply_filter_button" style="display:none;">{include file='admin/buttons/button.tpl' button_title=$lng.lbl_apply_filter|default:'Apply Filter' href="javascript: cw_submit_form('buffer_filter_form');" style='btn-green push-20 push-15-t'}</span>
<script type="text/javascript">
//<![CDATA[
{literal}
$(document).ready(function() {
$('#apply_filter_button').show();
});
{/literal}
//]]>
</script>

{if $current_target eq 'datahub_buffer_match' && !$is_blacklist_display}
&nbsp;&nbsp;|&nbsp;<a href="index.php?target=datahub_buffer_match_edit"><nobr>Edit Import Buffer Items</nobr></a>
{elseif $current_target eq 'datahub_buffer_match_edit'}
&nbsp;&nbsp;|&nbsp;<a href="index.php?target=datahub_buffer_match"><nobr>View Import Buffer List</nobr></a>
{/if}
</form>
