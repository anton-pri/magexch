<script type="text/javascript">
{literal}
var editor; // use a global for the submit and return data rendering in the examples

function dh_transform_web_path(path) {
    return path.replace('/www/saratogadev/','');
}
 
var dh_rating_options = [
  { label: "None", value: 0 },
{/literal}{section name=rt max=30 loop=101 step=-1}
{ldelim} label: "{$smarty.section.rt.index}", value: {$smarty.section.rt.index} {rdelim},
  {/section}{literal}{ label: "70", value: 70 }
];

$(document).ready(function() {
    editor = new $.fn.dataTable.Editor( {
        ajax: "index.php?target=dh_main_edit",
        table: "#example",
        fields: [ 
            {
                label: "ID",
                name: "ID",
                type: "readonly"
            }, 
{/literal}
{foreach from=$main_tbl_fields item=fld name=main_fields}{if $fld.field ne 'ID'}
           {ldelim}
                label: "{$fld.title|default:$fld.field|capitalize:true}",
{if $fld.type ne ''}
                type: "{$fld.type}",
    {if $fld.type eq "upload"}
                display: function ( file_id ) {ldelim}
                    return '<img class="edit_preview_img" src="../'+ dh_transform_web_path(dh_main_table.file( 'cw_datahub_main_data_images', file_id ).web_path)+'"/>';
                {rdelim},
                clearText: "Clear",
                noImageText: '<img class="edit_preview_img" src="../images/no_image.jpg" />',
    {else if $fld.type eq "select" && $fld.is_rating} 
                options: dh_rating_options,
    {/if}
{/if}

                name: "{$fld.field}"
           {rdelim}{if !$smarty.foreach.main_fields.last},{/if}
{/if}{/foreach}
{literal}
        ]
    } );

    // Buttons array definition to create previous, save and next buttons in
    // an Editor form
    var backNext = [
        {
            label: "&lt;",
            fn: function (e) {
                // On submit, find the currently selected row and select the previous one
                this.submit( function () {
                    var indexes = dh_main_table.rows( {search: 'applied'} ).indexes();
                    var currentIndex = dh_main_table.row( {selected: true} ).index();
                    var currentPosition = indexes.indexOf( currentIndex );
 
                    if ( currentPosition > 0 ) {
                        dh_main_table.row( currentIndex ).deselect();
                        dh_main_table.row( indexes[ currentPosition-1 ] ).select();
                    }
 
                    // Trigger editing through the button
                    dh_main_table.button( 1 ).trigger();
                }, null, null, false );
            }
        },
        'Save',
        {
            label: "&gt;",
            fn: function (e) {
                // On submit, find the currently selected row and select the next one
                this.submit( function () {
                    var indexes = dh_main_table.rows( {search: 'applied'} ).indexes();
                    var currentIndex = dh_main_table.row( {selected: true} ).index();
                    var currentPosition = indexes.indexOf( currentIndex );
 
                    if ( currentPosition < indexes.length-1 ) {
                        dh_main_table.row( currentIndex ).deselect();
                        dh_main_table.row( indexes[ currentPosition+1 ] ).select();
                    }
 
                    // Trigger editing through the button
                    dh_main_table.button( 1 ).trigger();
                }, null, null, false );
            }
        }
    ];

    var openVals;
    editor
        .on( 'open', function () {
            // Store the values of the fields on open
            openVals = JSON.stringify( editor.get() );
        } )
        .on( 'preBlur', function ( e ) {
            // On close, check if the values have changed and ask for closing confirmation if they have
            if ( openVals !== JSON.stringify( editor.get() ) ) {
                return confirm( 'You have unsaved changes. Are you sure you want to exit?' );
            }
        } );

    // Edit record
    $('#example').on('click', 'a.editor_edit', function (e) {
        e.preventDefault();
 
        editor.edit( $(this).closest('tr'), {
            title: 'Edit record',
            buttons: 'Update'
        } );
    } );

    $('#example tfoot th').each( function () {
        var title = $(this).text();

        if (title != 'Image') {
          $(this).html('<input type="text" style="width:98%; min-width:55px;" placeholder="'+title+'" />' );
        } 
    } );
 
    var dh_main_table = $('#example').DataTable( {
        dom: "Bfrtip",
        ajax: {
            url: "index.php?target=dh_main_edit",
            type: "POST"
        },
        serverSide: true,
        lengthMenu: [[10, 25, 50, 100, 200, 500], [10, 25, 50, 100, 200, 500]],
        scrollX: true,
        order: [[ 0, "desc" ]],

        columns: [
{/literal}
{foreach from=$main_tbl_fields item=fld name=display_main_fields}{if $fld.main_display}
{if !$fld.no_table_edit}

            {ldelim} data: "{$fld.field}", title: "{$fld.title|default:$fld.field|capitalize:true}", 
              {if $fld.is_image}
              render: function ( file_id ) {ldelim}
                return file_id ?
                  '<a href="" class="editor_edit" title="Edit"><img class="table_preview_img" src="../' + 
                  dh_transform_web_path((dh_main_table.file( 'cw_datahub_main_data_images', file_id ) != null)?dh_main_table.file( 'cw_datahub_main_data_images', file_id ).web_path:"images/no_image.jpg") + '"/></a>' : null;
              {rdelim},
              className: "center",
              defaultContent: "No image"
              {else}   
              render: function ( data, type, row ) {ldelim}
                if (row.{$fld.field} != null)
                    return '<a href="" class="editor_edit" title="Edit">' + row.{$fld.field} + '</a>';
                else 
                    return '';
              {rdelim} 
              {/if}
            {rdelim},

{else}
           {ldelim} data: "{$fld.field}", title: "{$fld.title|default:$fld.field|capitalize:true}" {rdelim}{if !$smarty.foreach.display_main_fields.last},{/if}

{/if}
{/if}{/foreach}
{literal}            
        ],
        select: true, 
        buttons: [
            { extend: "create", editor: editor },
            { extend: "edit",   editor: editor },
/*
            {
                extend: 'selected',
                text:   'Edit',
                action: function () {
                    var indexes = dh_main_table.rows( {selected: true} ).indexes();
 
                    editor.edit( indexes, {
                        title: 'Edit',
                        buttons: indexes.length === 1 ?
                            backNext :
                            'Save'
                    } );
                }
            },
*/
            { extend: "remove", editor: editor },
            'pageLength'
        ]
//*/
    } );

    dh_main_table.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );

   $("input[type=search]").css("width", "500px").css("height", "28px");

} );
{/literal}

</script>
{include file="addons/flexible_import/flexible_import_menu.tpl" active="2"}

{capture assign='header_footer_main_data'}
{foreach from=$main_tbl_fields item=fld name=display_main_fields}{if $fld.main_display}
<th>{$fld.title|default:$fld.field}</th>
{/if}{/foreach}
{/capture}

{capture name=section}
{capture name=block}
<table id="example" class="display" cellspacing="0" width="100%">
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
{include file='buttons/button.tpl' button_title=$lng.lbl_monthly_price_update|default:'Monthly price update' href="index.php?target=datahub_update_prices_monthly" style='btn-green'}

{*include file='buttons/button.tpl' button_title=$lng.lbl_price_recalc|default:'Price calculation' href="index.php?target=datahub_update_prices" style='btn-green*}

{*include file='buttons/button.tpl' button_title=$lng.lbl_check_transfer|default:'Transfer items preview' href="index.php?target=datahub_check_transfer_live" style='btn-green*}

{include file='buttons/button.tpl' button_title=$lng.lbl_transfer_live|default:'Transfer to live' href="index.php?target=datahub_calc_output" style='btn-green}

{include file='buttons/button.tpl' button_title=$lng.lbl_after_import_script|default:'After import script' href="index.php?target=datahub_transfer_after_import" style='btn-green'}

{include file='buttons/button.tpl' button_title=$lng.lbl_pos_update|default:'Pos Update (step 12a)' href="index.php?target=datahub_step_pos_update" style='btn-green'}

<br/><br/>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block extra='width="100%"'}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_edit_hub_products|default:'Edit Hub Items'}

