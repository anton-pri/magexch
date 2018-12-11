{if $buffer_search.map eq 'blacklist'}{assign var='is_blacklist_display' value=true}{/if}
{capture assign='match_items_count_options'}
<div style="float:right; font-size:13px;">
Display match items:&nbsp;
<select onchange="javascript: dh_change_match_count(this.value);">
{foreach from=$match_items_limit_options item=mil_o}
<option value="{$mil_o}" {if $current_match_items_limit eq $mil_o}selected='selected'{/if}>{$mil_o}</option>
{/foreach}
</select>
</div>
{/capture}
<script type="text/javascript">
var match_items_count_options = '{$match_items_count_options|escape:javascript}';
{literal}
var current_buffer_table_id = 0;
var is_interim = false;
var buffer_data_table;
function dh_item_select_popup(table_id) {

    if ($('#item_select_popup').length==0) {
        $('body').append('<div id="item_select_popup" style="overflow:hidden;"></div>');
        $('#item_select_popup').html("<iframe frameborder='no' width='950' height='540' src='index.php?target=datahub_item_select_popup'></iframe>")
    }

    current_buffer_table_id = table_id;
    // Show dialog
    sm('item_select_popup', 980, 580, false, 'Select Item');
}


function dh_save_match(table_id, key) {
    ajaxGet('index.php?target=dh_save_match&table_id='+table_id+'&key='+key);
}

function dh_change_match_count(mi_visible) {
    ajaxGet('index.php?target=dh_set_match_items_count&mi_visible='+mi_visible);

    //$("#refresh_link_"+product_id).parent().siblings(":empty").addClass("cp_data_loading");
    $('div[id^="match_items_list_"]').addClass("dh_data_loading");
    setTimeout(
         function () {
             buffer_data_table.ajax.reload(null, false); 
             setTimeout(function () {
                 buffer_data_table.ajax.reload(null, false); 
             }, 1000); 
         }, 1000);
}

$(document).ready(function() {

    // Setup - add a text input to each footer cell
    $('#buffer_data_table tfoot th').each( function () {
        var title = $(this).text();
        if (title != 'Match Items')
        $(this).html('<input type="text" style="width:98%; min-width:50px;" placeholder="'+title+'" />');
    } );
 
    buffer_data_table = $('#buffer_data_table').DataTable( {
        "processing": true,
        "serverSide": true,
        "lengthMenu": [[10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100, 200, 500,"All"]],
        "scrollX": true,
        "order": [[ 0, "desc" ]],
        //"searching": false,
        ajax: {
            url: "index.php?target=datahub_buffer_match_data",
            type: "POST"
        },
        "columnDefs": [
           { "width": "5%", "targets": [0]},
           { "sortable": false, "targets": -2}
        ]
    });

    buffer_data_table.on( 'draw', function () {
        if (typeof(cw_init_lng_tooltip) == 'function') {
            cw_init_lng_tooltip();
        }
    });
 
    // Apply the search
    buffer_data_table.columns().every( function () {
        var that = this;
        $( 'input', this.footer() ).on( 'keyup change', function (e) {
            if ( that.search() !== this.value && e.keyCode == 13) {
                that
                    .search( this.value )
                    .draw();
            }
        });
    });

    $('a.toggle-vis').on( 'click', function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = buffer_data_table.column( $(this).attr('data-column') );

        if (column.visible()) 
            $(this).addClass('datacolumn-toggle-disabled').attr('title', 'Show column'); 
        else
            $(this).removeClass('datacolumn-toggle-disabled').attr('title', 'Hide column');
 
        // Toggle the visibility
        column.visible( ! column.visible() );

        ajaxGet('index.php?target=dh_set_column_visibility&cfg_area=buffer&column='+$(this).attr('field-name')+'&visible='+column.visible());

    } );

{/literal}
{foreach from=$buffer_tbl_fields item=fld name=fldcyc}
    {if (in_array($fld.db, $pre_hide_columns))}$('#toggle_column_{$fld.dt}').trigger('click');{/if}
{/foreach}
{literal}

    $("input[type=search]").parent().html(match_items_count_options);

});

function popup_import_buffer_image_preview(e) {
    var img_url = $(e).find('img').attr('src');
    $("#preview_image_popup_img").attr('src', img_url);
    $("#preview_image_popup").dialog({
        modal: true
    });
}

{/literal}

</script>
<div id="preview_image_popup"  style='display:none; text-align:center;'>
<img id="preview_image_popup_img" />
</div>
{include file="addons/flexible_import/flexible_import_menu.tpl" active="2"}

{capture name=section}

{capture name=block1}
{include file="addons/flexible_import/datahub/buffer_filter_form.tpl"}

<div>
<b>{$lng.lbl_show_hide_column|default:'Show/Hide column'}:</b>
{foreach from=$buffer_tbl_fields item=fld name=fldcyc}{assign var='_fld_db' value=$fld.db} 
<a class="toggle-vis" field-name="{$fld.db}" id="toggle_column_{$fld.dt}" style="cursor: pointer;" title="Hide column" data-column="{$fld.dt}">{if $fld.db eq 'Match Items' && $is_blacklist_display}Action{else}{$dh_buffer_table_fields.$_fld_db.title|default:$fld.db|capitalize:true}{/if}</a>{if !$smarty.foreach.fldcyc.last} - {/if}
{/foreach}
</div>
{capture assign='header_footer_buffer_data'}
{foreach from=$buffer_tbl_fields item=fld}{assign var='_fld_db' value=$fld.db}
                <th {if $fld.width ne ''}width="{$fld.width}%"{/if}>{if $fld.db eq 'Match Items' && $is_blacklist_display}Action{else}{$dh_buffer_table_fields.$_fld_db.title|default:$fld.db|capitalize:true}{/if}</th>
{/foreach}
{/capture}
<div>
<table id="buffer_data_table" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
            {$header_footer_buffer_data}
            </tr>
        </thead>
        <tfoot>
            <tr>
            {$header_footer_buffer_data}
            </tr>
        </tfoot>
    </table>
<br />
<form name="buffer_match_form" method="post" action="index.php?target=datahub_buffer_match">
<input type="hidden" name="action" value="save_match" />
{if $is_blacklist_display eq ''}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_clean|default:'Clean Buffer' href="javascript:if (confirm('All import buffer items will be deleted!')) cw_submit_form('buffer_match_form', 'clean_buffer_data');" style='btn-green'}

{if $config.flexible_import.fi_sheduled_generate_matches eq 'N' || $smarty.get.all_buttons eq 'Y' || 1}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_generate_matches|default:'Find matches for all' href="index.php?target=datahub_gen_matches" style='btn-green'}

{include file='admin/buttons/button.tpl' button_title=$lng.lbl_generate_matches|default:'Find matches for new' href="index.php?target=datahub_gen_matches&new_only=1" style='btn-green'}
{/if}

{if $smarty.get.all_buttons eq 'Y' || 1}
{if $config.flexible_import.flex_import_auto_update ne 'Y' || $smarty.get.all_buttons eq 'Y' || 1}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update_linked_data|default:'Update Mapped Products' href="javascript:if (confirm('This will update main items table with data from import buffer and delete linked items from buffer')) cw_submit_form('buffer_match_form', 'update_linked_data');" style='btn-green'}
{/if}
{/if}

{*if $smarty.get.all_buttons eq 'Y' || 1}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_load_pos_and_update|default:'Load POS data' href="index.php?target=datahub_pos_update" style='btn-green'}
{/if*}

{if $config.flexible_import.fi_datahub_show_data_load_buttons_working_buffer eq 'Y' || $smarty.get.all_buttons eq 'Y'}
{if $config.flexible_import.fi_datahub_autoload_bevamonthly ne 'Y' || $smarty.get.all_buttons eq 'Y'}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_beva_monthly|default:'BevA Monthly' href="index.php?target=datahub_beva_monthly" style='btn-green'}
{/if}

{if $config.flexible_import.fi_datahub_autoload_bevadaily ne 'Y' || $smarty.get.all_buttons eq 'Y'}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_beva_daily|default:'Import Beverage Media' href="index.php?target=datahub_beva_daily" style='btn-green'}
{/if}

{if $config.flexible_import.fi_datahub_autoload_vias ne 'Y' || $smarty.get.all_buttons eq 'Y'}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_vias|default:'Import Vias' href="index.php?target=datahub_vias" style='btn-green'}
{/if}

{if $config.flexible_import.fi_datahub_autoload_profiles ne 'Y' || $smarty.get.all_buttons eq 'Y'}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_check_and_load_new_files|default:'Check and load new files' href="index.php?target=datahub_buffer_match&reload_profiles_if_new=Y" style='btn-green'}
{/if}
{/if}


{/if}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_apply href="javascript:cw_submit_form('buffer_match_form', 'apply_match');" style='btn-green'}&nbsp;

{if $is_blacklist_display eq ''}
<div style="height:5px;"></div>
{*include file='admin/buttons/button.tpl' button_title=$lng.lbl_save_nonstock_update_links|default:'Save Non-stock update links' href="javascript:if (confirm('This action will save the Non-stock update links!')) cw_submit_form('buffer_match_form', 'apply_update_nonstock_match');" style='btn-green'}&nbsp;
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update_nonstock_data|default:'Update Non-stock data' href="index.php?target=datahub_update_nonstock&action=prepare_preview" style='btn-green'*}

{/if}
</form>
</div>
<br/>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block1 title=$lng.lbl_filter_mapped_products|default:'Filter Products' inline_style_content="padding-top:0px;"}



{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_match_imported_products|default:'Match Imported Items'}
