<script type='text/javascript'>
//var buffer_table_id = '{$buffer_table_id}';
var current_sel_item = 0;
var dh_current_choice_text = '';
var err_datahub_choose_item = "{$lng.lbl_datahub_choose_item|default:'Please choose an item first'}";
{literal}
function setDatahubItem () {
    var buffer_table_id;

    buffer_table_id = window.parent.current_buffer_table_id;

    if (current_sel_item) {
        window.parent.document.getElementById('manual_select_text_'+buffer_table_id).value='#' + current_sel_item + ' ' + dh_current_choice_text;
 
        if (typeof(window.parent.dh_load_merge_src) == 'function') {
            window.parent.dh_load_merge_src(current_sel_item);
        }
        $('#matchassign_'+buffer_table_id+'_9999999', parent.document).prop('checked', true);
        ajaxGet('index.php?target=dh_save_match&table_id='+buffer_table_id+'&key=9999999&manual_sel_id='+current_sel_item);

        window.parent.hm('item_select_popup'); 
    } else {
        alert(err_datahub_choose_item); 
    }
        
    return false;
}

function closeDatahubItemsPopup() {
    var buffer_table_id;
    buffer_table_id = window.parent.current_buffer_table_id;
    $('#matchassign_'+buffer_table_id+'_0', parent.document).prop('checked', true);

    window.parent.hm('item_select_popup');
}

function dh_clean (val) {

    if (val != null) 
        if (val.trim() != '')  
            return val + ' ';

    return '';
}

$(document).ready(function() {

    // Setup - add a text input to each footer cell
    $('#main_data_table tfoot th').each( function () {
        var title = $(this).text();
        $(this).html('<input type="text" style="width:98%; min-width:50px;" placeholder="'+title+'" />');
    } );
 
    var main_data_table = $('#main_data_table').DataTable( {
        "processing": true,
        "serverSide": true,
        "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
        "scrollX": true,
        "order": [[ 0, "desc" ]],
        ajax: {
            url: "index.php?target=datahub_item_select_popup_data",
            type: "POST"
        },
        "columnDefs": [
           {/literal}{foreach from=$main_tbl_fields item=fld}{if $fld.item_select_popup_searchable_off}{ldelim}"searchable": false, "targets": {$fld.dt}{rdelim},{/if}{/foreach}{literal}
           { "width": "5%", "targets": [0]}
        ]
    });

    $('#main_data_table tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
            //var mdt_data = main_data_table.row('.selected').data();
        }
        else {
            main_data_table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            var mdt_data = main_data_table.row('.selected').data();
            current_sel_item = mdt_data[0];
            //$preview_columns = array('ID','Producer','name','Vintage','size','country','Region','Appellation','sub_appellation');
            dh_current_choice_text = dh_clean(mdt_data[2]) + dh_clean(mdt_data[3]) + dh_clean(mdt_data[4]) + dh_clean(mdt_data[5]) + dh_clean(mdt_data[7]) + dh_clean(mdt_data[9]) + dh_clean(mdt_data[11]) + dh_clean(mdt_data[12]) + dh_clean(mdt_data[13]);
            $('#dh_current_choice_text').html('<b>Selected Item: #' +mdt_data[0] + '</b> ' + dh_current_choice_text);
        }
    } );

    // Apply the search
    main_data_table.columns().every( function () {
        var that = this;
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        });
    });

    $('a.toggle-vis').on( 'click', function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = main_data_table.column( $(this).attr('data-column') );

        if (column.visible()) 
            $(this).addClass('datacolumn-toggle-disabled').attr('title', 'Show column'); 
        else
            $(this).removeClass('datacolumn-toggle-disabled').attr('title', 'Hide column');
 
        // Toggle the visibility
        column.visible( ! column.visible() );
        ajaxGet('index.php?target=dh_set_column_visibility&cfg_area=popup_main&column='+$(this).attr('field-name')+'&visible='+column.visible());
    } );

{/literal}
{foreach from=$main_tbl_fields item=fld name=fldcyc}
    {if !$fld.popup_main_display}$('#toggle_column_{$fld.dt}').trigger('click');{/if}
{/foreach}
{literal}

    $("input[type=search]").css("width", "500px").css("height", "28px");

});

{/literal}
</script>

<div>
<b>{$lng.lbl_show_hide_column|default:'Show/Hide column'}:</b>
{foreach from=$main_tbl_fields item=fld name=fldcyc} 
<a class="toggle-vis" field-name='{$fld.field}' id="toggle_column_{$fld.dt}" style="cursor: pointer;" title="Hide column" data-column="{$fld.dt}">{$fld.field|replace:'_':' '|replace:'bot ':'bottle '|capitalize:true}</a>{if !$smarty.foreach.fldcyc.last} - {/if}
{/foreach}
</div>
<br /><br />
{capture assign='header_footer_main_data'}
{foreach from=$main_tbl_fields item=fld}
                <th {if $fld.width ne ''}width="{$fld.width}%"{/if}>{$fld.field|capitalize:true}</th>
{/foreach}
{/capture}
<table id="main_data_table" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>
        {$header_footer_main_data}
        </tr>
    </thead>
    <tfoot>
        <tr>
        {$header_footer_main_data}
        </tr>
    </tfoot>
</table>

<br/>
<div style="float:left" id="dh_current_choice_text"><b>Selected Item:</b>&nbsp;{$lng.lbl_none}</div>
<div style="float:right">
{include file='buttons/button.tpl' button_title=$lng.lbl_choose href="javascript: setDatahubItem();" style='btn-green'}&nbsp;{include file='buttons/button.tpl' button_title=$lng.lbl_close href="javascript: closeDatahubItemsPopup();" style='btn-danger'}</div>
<br />
<br />
