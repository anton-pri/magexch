{assign var='show_toggle_links' value=1}
<script type="text/javascript">
{literal}
var editor; // use a global for the submit and return data rendering in the examples

function dh_uns_transform_web_path(path) {
    return path.replace('/www/saratogadev/','');
}

$(document).ready(function() {
    editor = new $.fn.dataTable.Editor( {
        ajax: "index.php?target=dh_update_nonstock",
        table: "#example",
        fields: [ 
            {
                label: "table_id",
                name: "table_id",
                type: "readonly"
            },
{/literal}
{foreach from=$uns_tbl_fields item=fld name=uns_fields}{if $fld.field ne 'table_id'}
           {ldelim}
                label: "{$fld.title|default:$fld.field|capitalize:true}",
    {if $fld.type ne ''}
                type: "{$fld.type}",
    {else}
                type: "textarea",
    {/if}
                name: "{$fld.field}"
           {rdelim}{if !$smarty.foreach.uns_fields.last},{/if}
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
                    var indexes = dh_uns_table.rows( {search: 'applied'} ).indexes();
                    var currentIndex = dh_uns_table.row( {selected: true} ).index();
                    var currentPosition = indexes.indexOf( currentIndex );
 
                    if ( currentPosition > 0 ) {
                        dh_uns_table.row( currentIndex ).deselect();
                        dh_uns_table.row( indexes[ currentPosition-1 ] ).select();
                    }
 
                    // Trigger editing through the button
                    dh_uns_table.button( 1 ).trigger();
                }, null, null, false );
            }
        },
        'Save',
        {
            label: "&gt;",
            fn: function (e) {
                // On submit, find the currently selected row and select the next one
                this.submit( function () {
                    var indexes = dh_uns_table.rows( {search: 'applied'} ).indexes();
                    var currentIndex = dh_uns_table.row( {selected: true} ).index();
                    var currentPosition = indexes.indexOf( currentIndex );
 
                    if ( currentPosition < indexes.length-1 ) {
                        dh_uns_table.row( currentIndex ).deselect();
                        dh_uns_table.row( indexes[ currentPosition+1 ] ).select();
                    }
 
                    // Trigger editing through the button
                    dh_uns_table.button( 1 ).trigger();
                }, null, null, false );
            }
        }
    ];
    var openVals;
    editor
        .on( 'open', function () {
            // Store the values of the fields on open
            openVals = JSON.stringify( editor.get() );
            $('div.DTE_Footer').css( 'text-indent', -1 );
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
    var dh_uns_table = $('#example').DataTable( {
        dom: "Bfrtip",
        ajax: {
            url: "index.php?target=dh_update_nonstock",
            type: "POST"
        },
        serverSide: true,
    //    lengthMenu: [[10, 25, 50, 100, 200, 500], [10, 25, 50, 100, 200, 500]],
        lengthMenu: [[10, 25, 50], [10, 25, 50]],
        scrollX: true,
        order: [[ 0, "desc" ]],

        columns: [
{/literal}
{foreach from=$uns_tbl_fields item=fld name=display_uns_fields}
{if !$fld.no_table_edit}
            {ldelim} name: "{$fld.field}", data: "{$fld.field}", title: "{$fld.title|default:$fld.field|capitalize:true}", 
              {if $fld.is_image}
/*
              render: function ( file_id ) {ldelim}
                return file_id ?
                  '<a href="" class="editor_edit" title="Edit"><img class="table_preview_img" src="../' + 
                  dh_uns_transform_web_path((dh_uns_table.file( 'cw_datahub_update_nonstock_preview_images', file_id ) != null)?dh_uns_table.file( 'cw_datahub_update_nonstock_preview_images', file_id ).web_path:"images/no_image.jpg") + '"/></a>' : null;
              {rdelim},
*/
              render: function (data, type, row ) {ldelim}
                if (row.{$fld.field} != null) {ldelim}
                   var img_link = row.{$fld.field}; 
                   return "<div class='import_buffer_image_box'><a class='import_buffer_image_preview' onclick='javascript: popup_import_buffer_image_preview(this)'><img class='import_buffer_image_tmbn' src='"+img_link+"' alt=''  title='Click to popup' /></a></div>";  
                {rdelim}   
              {rdelim},
              className: "center",
              defaultContent: "No image"
              {else}   
              render: function ( data, type, row ) {ldelim}
                if (row.{$fld.field} != null) {ldelim}
                    var link_title = row.{$fld.field};
                    {if $fld.type eq 'textarea'}
                    link_title = jQuery.trim(link_title).substring(0, 42).split(" ").slice(0, -1).join(" ") + "...";
                    {else} 
                    if (link_title.length > 120)
                        link_title = jQuery.trim(link_title).substring(0, 100).split(" ").slice(0, -1).join(" ") + "...";  
                    {/if} 
                    return '<a href="" class="editor_edit" title="Edit">' + link_title + '</a>';
                {rdelim} else 
                    return '';
              {rdelim} 
              {/if}
            {rdelim},

{else}
           {ldelim} data: "{$fld.field}", title: "{$fld.title|default:$fld.field|capitalize:true}" {rdelim}{if !$smarty.foreach.display_uns_fields.last},{/if}

{/if}
{/foreach}
{literal}            
        ],
        select: true, 
        buttons: [
/*            { extend: "create", editor: editor },*/
            { extend: "edit",   editor: editor },
            { extend: "remove", editor: editor },
            'pageLength'
        ]
    } );

    dh_uns_table.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );

    $('a.toggle-vis').on( 'click', function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = dh_uns_table.column( $(this).attr('data-column') );

        if (column.visible()) 
            $(this).addClass('datacolumn-toggle-disabled').attr('title', 'Show column'); 
        else
            $(this).removeClass('datacolumn-toggle-disabled').attr('title', 'Hide column');
 
        // Toggle the visibility
        column.visible( ! column.visible() );
        ajaxGet('index.php?target=dh_set_column_visibility&cfg_area=update_nonstock&column='+$(this).attr('field-name')+'&visible='+column.visible());
    } );

{/literal}
{if $show_toggle_links}
{foreach from=$uns_tbl_fields item=fld name=fldcyc}
    {if (in_array($fld.field, $pre_hide_columns))}$('#toggle_column_{$fld.field}').trigger('click');{/if}
{/foreach}
{/if}
{literal}

    $("input[type=search]").css("width", "500px").css("height", "28px");
} );

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

{capture assign='header_footer_uns_data'}
{foreach from=$uns_tbl_fields item=fld name=display_uns_fields}
<th>{$fld.title|default:$fld.field}</th>
{/foreach}
{/capture}

{capture name=section}
{capture name=block}

<div {if !$show_toggle_links}style="display:none;"{/if}>

{*$uns_tbl_fields|@debug_print_var*}

<b>{$lng.lbl_show_hide_column|default:'Show/Hide column'}:</b>
{foreach from=$uns_tbl_fields item=fld name=fldcyc}
<a class="toggle-vis" field-name='{$fld.field}' id="toggle_column_{$fld.field}" style="cursor: pointer;" title="Hide column" data-column="{$fld.dt}">{$fld.title|default:$fld.field|replace:'_':' '|replace:'bot ':'bottle '|replace:'>>':''|capitalize:true}</a>{if !$smarty.foreach.fldcyc.last} - {/if}
{/foreach}
<br><br>
</div>

<table id="example" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>
{$header_footer_uns_data}
        </tr>
    </thead>
    <tfoot>
        <tr>
{$header_footer_uns_data}
        </tr>
    </tfoot>
</table>

<br />
<form name="update_nonstock_form" method="post" action="index.php?target=datahub_update_nonstock">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="is_interim" value="{$smarty.get.is_interim}" />
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript:if (confirm('This will update main items table with data from import buffer')) cw_submit_form('update_nonstock_form', 'update');" style='btn-green push-15-r'}
&nbsp;
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update_and_delete|default:'Update and delete buffer items' href="javascript:if (confirm('This will update main items table with data from import buffer and delete linked items from buffer')) cw_submit_form('update_nonstock_form', 'update_and_clean');" style='btn-green push-15-r'}
</form>

<br/>

<br/><br/>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block extra='width="100%"'}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_update_nonstock_data|default:'Update non-stock data'}
