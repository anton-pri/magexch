<input type='hidden' name='saved_table_id' value='{$table_id}' />
<input type='hidden' name='mode' value='update' />
<input type='hidden' name='add_as_new' value='N' />
<input type='hidden' name='switch2next' value='' />

<div style="overflow: hidden;margin-top:20px;">
  <div style='float:left; width: 48%;padding-right: 2%'>
    <h3 class="block-title">{$buffer_item.display}</h3>
    <div style='text-align:left;padding-bottom:10px;padding-top:10px'>
    	{include file='buttons/button.tpl' button_title=$lng.lbl_save href="javascript:dh_save_current_item(0);void(0);"}&nbsp;
    	{include file='buttons/button.tpl' button_title=$lng.lbl_save_and_add_as_new|default:'Save & Add as new' href="javascript:dh_save_current_item(1);void(0);"}
	</div>
  </div>
  <div style='float:right; width: 50%;' class="item_radio_inputs">
{assign var='merge_src_cnt' value=0}
    {foreach from=$merge_src key=mID item=merge_item_display}
{if $merge_src_cnt le 3}
       <div class="item_radio" style="margin-bottom:0px;"><input type='radio' value='{$mID}' class='merge_source_select' name='merge_sources_{$table_id}' id='merge_sources_{$table_id}_{$mID}'><label for='merge_sources_{$table_id}_{$mID}'>{$merge_item_display}</label></div>
{/if}
{assign var='merge_src_cnt' value=$merge_src_cnt+1}
    {/foreach}
    {assign var='manual_sel_k' value='9999999'}
    <input id='matchassign_{$table_id}_{$manual_sel_k}' type='radio' name='merge_sources_{$table_id}' value='{$manual_sel_k}' onclick="javascript: dh_item_select_popup({$table_id});"><label for='matchassign_{$table_id}_{$manual_sel_k}'>Select:&nbsp;<input type='text' class='dh_manual_select_item' id='manual_select_text_{$table_id}' value='' readonly='readonly' onclick='javascript: dh_item_select_popup({$table_id});' /></label>
  </div>
</div> 

<div>
{foreach from=$dh_buffer_table_fields key=fname item=field_def name=fldcyc}
{assign var='htmlfname' value=$fname|replace:'&':'and'}
{if $field_def.no_edit ne 1}
<a class="toggle-vis" field-name="{$htmlfname}" id="toggle_column_{$htmlfname}" style="cursor: pointer;" title="Hide column">{$field_def.title|default:$fname|replace:'_':' '|capitalize}</a>{if !$smarty.foreach.fldcyc.last} - {/if}
<!--
<a id="eir_link_{$htmlfname}" class="toggle-vis" href="javascript: dh_field_toggle('{$htmlfname}');void(0);" >{$htmlfname}</a>&nbsp;&nbsp;
-->
{/if}
{/foreach}
</div> 

<div style="width:100%;height:500px;overflow-x:hidden;overflow-y:scroll;border: 1px solid #cacaca;margin-top: 20px;">
<table style="width:100%" class="header">
<tr>
  <th>Import Table Field<br /><a href="javascript: dh_field_reset_all(); void(0);">Reset All</a></th>
  <th width="25%">Current Value</th>
  <th width="25%">New Value</th>
  <th>Main Data Field<br /><a href="javascript: dh_automerge_fields(); void(0);">Merge</a></th>
  <th width="25%">Main Data Value</th>
</tr>
{foreach from=$dh_buffer_table_fields key=fname item=field_def}
{assign var='htmlfname' value=$fname|replace:'&':'and'}
{if $field_def.no_edit ne 1}
<tr id="edit_item_row_{$htmlfname}">
  <td align='left' nowrap><b>{$field_def.title|default:$fname|replace:'_':' '|capitalize}</b>
    {if !$field_def.read_only}<br />
     <a href="javascript: dh_field_reset('{$htmlfname}');void(0);">Reset</a>
    {/if}
  </td>
  <td align='left'>
    {if $field_def.edit_type eq 'image'}
      <div id='old_val_{$table_id}_{$htmlfname}_path'>{$buffer_item.image.web_path|default:'/images/no_image.jpg'}</div>
      <img id='old_val_{$table_id}_{$htmlfname}' src="{$buffer_item.image.web_path|default:'/images/no_image.jpg'}" style="max-width:100px;max-height:100px;text-align:center;"/>
    {else}
      <div id='old_val_{$table_id}_{$htmlfname}'>{$buffer_item.$fname}</div>
    {/if}  
  </td>
  <td align='left'>
   {if $field_def.read_only}
       <i>read only</i>
   {else}
       {if $field_def.edit_type eq 'numeric'}
         <input style="width:98%" type='text' id='edit_buffer_item_{$table_id}_{$htmlfname}' name='edit_buffer_item[{$table_id}][{$fname}]' value='{$buffer_item.$fname|default:0}' />
       {elseif $field_def.edit_type eq 'mediumtext'}
         <textarea style="width:98%" rows="2" id='edit_buffer_item_{$table_id}_{$htmlfname}' name='edit_buffer_item[{$table_id}][{$fname}]'>{$buffer_item.$fname}</textarea>
       {elseif $field_def.edit_type eq 'largetext'}
         <textarea style="width:98%" rows="5" id='edit_buffer_item_{$table_id}_{$htmlfname}' name='edit_buffer_item[{$table_id}][{$fname}]'>{$buffer_item.$fname}</textarea>
       {elseif $field_def.edit_type eq 'image'}
         <input style="width:98%" type="text" id='edit_buffer_item_{$table_id}_{$htmlfname}_inp' name='edit_buffer_item[{$table_id}][{$fname}]' value="{$buffer_item.image.web_path|default:'/images/no_image.jpg'}" onkeyup="javascript: $('#edit_buffer_item_{$table_id}_{$htmlfname}').attr('src',this.value)" />
         <br>
         <img id='edit_buffer_item_{$table_id}_{$htmlfname}' src="{$buffer_item.image.web_path|default:'/images/no_image.jpg'}" style="max-width:100px;max-height:100px;"/>
         <div style="display:none;" id='edit_buffer_item_{$table_id}_{$htmlfname}_path'>{$buffer_item.image.web_path|default:'/images/no_image.jpg'}</div>
       {else}
         <input style="width:98%" type='text' id='edit_buffer_item_{$table_id}_{$htmlfname}' name='edit_buffer_item[{$table_id}][{$fname}]' value='{$buffer_item.$fname}' />
        {/if}
   {/if}
  </td>
  <td align='left' style='padding-left:15px;' nowrap><b>{$bmerge_config.$fname.mfield|replace:'_':' '|capitalize}</b><br />{if !$field_def.read_only && $bmerge_config.$fname.mfield ne ''}<a class='merge_manual_copy' href="javascript: dh_manual_copy_field('{$htmlfname}');void(0);">Copy</a>{/if}</td>
  <td align='left'>
    {if $htmlfname ne 'image'}
      <div style='padding-left:10px' id='merge_src_{$table_id}_{$htmlfname}' align='left'></div>
    {else}
       <div id='merge_src_{$table_id}_image_path'>/images/no_image.jpg</div>
       <img id='merge_src_{$table_id}_image' src="/images/no_image.jpg" style="max-width:100px;max-height:100px;"/> 
    {/if}
  </td>
</tr>
{/if}
{/foreach}
</table>
</div>
{*$bmerge_config|@debug_print_var*}
<div id='dh_buffer_edit_merge_src'></div>
<script>
var table_id = '{$table_id}';
var current_merge_ID = '{foreach from=$merge_src key=mID item=merge_item_display name=midisp}{if $smarty.foreach.midisp.first}{$mID}{/if}{/foreach}';
var current_buffer_table_id = 0;
var is_interim = false;
{literal}
function dh_item_select_popup(table_id) {

    if ($('#item_select_popup').length==0)
        $('body').append('<div id="item_select_popup" style="overflow:hidden;"></div>');

    $('#item_select_popup').html("<iframe frameborder='no' width='950' height='540' src='index.php?target=datahub_item_select_popup'></iframe>")

    current_buffer_table_id = table_id;
    // Show dialog
    sm('item_select_popup', 980, 580, false, 'Select Item');
}

function dh_load_merge_src(ID) {
    ajaxGet('index.php?target=dh_buffer_item_edit_merge_src&table_id=' + table_id + '&mID=' + ID);
    current_merge_ID = ID;
}

function dh_manual_copy_field(bfield) {
    if (bfield == 'image') {
        $('#edit_buffer_item_'+table_id+'_image').attr('src', $('#merge_src_'+table_id + '_image').attr('src'));
        $('#edit_buffer_item_'+table_id+'_image_path').html($('#merge_src_'+table_id + '_image').attr('src'));
        $('#edit_buffer_item_'+table_id+'_image_inp').val($('#merge_src_'+table_id + '_image').attr('src'));   
    } else { 
        $('#edit_buffer_item_'+table_id+'_'+bfield).val($('#merge_src_'+table_id + '_' + bfield).text());
    }
}

function dh_automerge_fields() {
{/literal}
{foreach from=$bmerge_config key=bfname item=bfielddata}
{assign var='htmlfname' value=$bfname|replace:'&':'and'}
{if $bfielddata.merge_cond eq 'A'}
  $('#edit_buffer_item_'+table_id+'_{$htmlfname}').val($('#merge_src_'+table_id + '_{$htmlfname}').text());
{else}
  {if $bfielddata.merge_cond eq 'E'}
    if ($('#merge_src_'+table_id + '_{$htmlfname}').text() != '' && $('#edit_buffer_item_'+table_id+'_{$htmlfname}').val() == '') 
      $('#edit_buffer_item_'+table_id+'_{$htmlfname}').val($('#merge_src_'+table_id + '_{$htmlfname}').text());
  {/if}
{/if}
{/foreach}
{literal}
}

function dh_field_reset_all () {
{/literal}
    {foreach from=$dh_buffer_table_fields key=fname item=field_def}
      {assign var='htmlfname' value=$fname|replace:'&':'and'}
      {if !$field_def.no_edit && !$field_def.read_only}
        dh_field_reset('{$htmlfname}');
      {/if}
    {/foreach} 
{literal}
}

function dh_field_reset(bfield) {
    if (bfield == 'image') {
        $('#edit_buffer_item_'+table_id+'_image').attr('src', $('#old_val_'+table_id + '_image').attr('src'));
        $('#edit_buffer_item_'+table_id+'_image_path').html($('#old_val_'+table_id + '_image').attr('src'));
        $('#edit_buffer_item_'+table_id+'_image_inp').val($('#old_val_'+table_id + '_image').attr('src'));   
    } else { 
        $('#edit_buffer_item_'+table_id+'_'+bfield).val($('#old_val_'+table_id + '_' + bfield).text());
    }
}

/*
function dh_field_toggle(bfield) {
   var eir_elem_id = '#edit_item_row_' + bfield; 
   var eir_link_id = '#eir_link_' + bfield;
   $(eir_elem_id).toggle();  
   if ($(eir_elem_id).is(":visible"))
       $(eir_link_id).removeClass('datacolumn-toggle-disabled').blur();  
   else
       $(eir_link_id).addClass('datacolumn-toggle-disabled').blur();
       
}
*/

$(document).ready(function() {
//    dh_load_merge_src(current_merge_ID);

    $('#merge_sources_'+table_id+'_'+current_merge_ID).trigger('click');
    dh_load_merge_src(current_merge_ID);

    $('input.merge_source_select').on('click', function(){
      var mID = $(this).val(); 
      dh_load_merge_src(mID);
    }); 


    $('a.toggle-vis').on( 'click', function (e) {
        e.preventDefault();
 
        var eir_elem_id = '#edit_item_row_' + $(this).attr('field-name');

        if ($(eir_elem_id).is(":visible")) 
            $(this).addClass('datacolumn-toggle-disabled').attr('title', 'Show column'); 
        else
            $(this).removeClass('datacolumn-toggle-disabled').attr('title', 'Hide column');
 
        // Toggle the visibility
        $(eir_elem_id).toggle();
//alert('index.php?target=dh_set_column_visibility&cfg_area=buffer_edit&column='+$(this).attr('field-name')+'&visible='+$(eir_elem_id).is(":visible"));
        ajaxGet('index.php?target=dh_set_column_visibility&cfg_area=buffer_edit&column='+$(this).attr('field-name')+'&visible='+$(eir_elem_id).is(":visible"));

    } );

{/literal}
{foreach from=$dh_buffer_table_fields key=fname item=field_def name=fldcyc}
{assign var='htmlfname' value=$fname|replace:'&':'and'}
    {if (in_array($htmlfname, $pre_hide_columns))}$('#toggle_column_{$htmlfname}').trigger('click');{/if}
{/foreach}
{literal}


});
{/literal}
</script>
