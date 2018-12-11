<script type="text/javascript">
var current_buffer_item_table_id = -1;
var buffer_item_ids = [{foreach from=$buffer_items item=bi name=bi_vals}{$bi.table_id}{if !$smarty.foreach.bi_vals.last},{/if}{/foreach}];
var recent_add_as_new = false;
{*foreach from=$buffer_items item=bi}
buffer_item_ids.push('{$bi.table_id}');
{/foreach*}
current_buffer_item_table_id = {if $presel_buffer_item gt 0}{$presel_buffer_item}{else}buffer_item_ids[0]{/if};
{literal}

function dh_save_current_item_callback(obj) {
//    console.log(obj);
//    alert('Saved Buffer Item');
    if (!recent_add_as_new) 
        dh_select_buffer_item(current_buffer_item_table_id);
    else
        location.reload();
}

function dh_save_current_item (add_as_new) {

    if (add_as_new) {
        $("[name=add_as_new]").val('Y');
        $("[name=switch2next]").val(dh_find_next_prev(current_buffer_item_table_id, true));  
    } else
        $("[name=add_as_new]").val('N');

    $('input[name="edit_buffer_item['+current_buffer_item_table_id+'][image]"]').val($('#edit_buffer_item_'+current_buffer_item_table_id+'_image').attr('src'));

    recent_add_as_new = add_as_new;

    submitFormPart('dh_buffer_edit_area', dh_save_current_item_callback);
}

function dh_load_edit_area(table_id) {
    ajaxGet('index.php?target=dh_buffer_item_edit&mode=view&table_id=' + table_id, 'dh_buffer_edit_area');
}

function dh_select_buffer_item(table_id) {
    dh_load_edit_area(table_id);

    if (current_buffer_item_table_id != -1)
        $("[table-id="+current_buffer_item_table_id+"]").removeClass('bi-selected');
 
    $("[table-id="+table_id+"]").addClass('bi-selected');
    current_buffer_item_table_id = table_id;

    var curr_idx = -1;// = buffer_item_ids.indexOf(current_buffer_item_table_id);

    for (var x in buffer_item_ids) {
      if (buffer_item_ids[x] == current_buffer_item_table_id) 
        curr_idx = x;
    }

    if (curr_idx < 4)
        $("#buffer_items_nav_box").scrollLeft(0);
    else 
        $("#buffer_items_nav_box").scrollLeft(146*(curr_idx-4));
     
}

function dh_find_next_prev(table_id, gonext) {
    var idx = buffer_item_ids.indexOf(table_id);
    if (gonext)  
        idx++; 
    else 
        idx--;  
    
    if (idx < 0)
       idx = buffer_item_ids.length-1;
    if (idx >= buffer_item_ids.length)
       idx = 0;

    return buffer_item_ids[idx];
}

function dh_goto_buffer_item(gonext) {
    var new_table_id = dh_find_next_prev(current_buffer_item_table_id, gonext);
    dh_select_buffer_item(new_table_id); 
}


$(document).ready(function() {
  $('div.buffer_item_list').on('click', function(){ 
      var table_id = $(this).attr('table-id');
      dh_select_buffer_item(table_id);
  });

  if (current_buffer_item_table_id != -1)
      dh_select_buffer_item(current_buffer_item_table_id);
});
{/literal}
</script>

{include file="addons/flexible_import/flexible_import_menu.tpl" active="2"}

{capture name=section}

{capture name=block1}

{include file="addons/flexible_import/datahub/buffer_filter_form.tpl"}

<div style="border: 1px solid #cacaca">

  <div id="buffer_items_nav_box" style="height:110px; width:100%; overflow-y: hidden; overflow-x:scroll;">
    <div style="width: {math equation="146*x" x=$buffer_items_count}px;height:100px;">
      {foreach from=$buffer_items item=bi}
        <div class="buffer_item_list" table-id="{$bi.table_id}">
         <span style="display: inline-block;vertical-align: middle;" >
         {$bi.display}
         </span> 
        </div>
      {/foreach}
    </div>
  </div>
</div>


<div style='margin-bottom:10px;margin-top:10px;text-align:center'>
{include file='buttons/button.tpl' button_title=$lng.lbl_arrow_previous|default:'<' href="javascript:dh_goto_buffer_item(false);"}&nbsp;
<span style='position:relative; top:2px;'>Items count:&nbsp;{$buffer_items_count}&nbsp;</span>
&nbsp;{include file='buttons/button.tpl' button_title=$lng.lbl_arrow_next|default:'>' href="javascript:dh_goto_buffer_item(true);"}
</div>
{*<form method='post' action='index.php?target=dh_buffer_item_edit&mode=update' name='buffer_edit_area_form'>
<input type='hidden' name='mode' value='update' />*}
<div id="dh_buffer_edit_area" style="top:0px;">
</div>
{*</form>*}

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block1 title=$lng.lbl_filter_mapped_products|default:'Filter Mapped Products'}


{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_match_imported_products|default:'Edit Imported Items'}

