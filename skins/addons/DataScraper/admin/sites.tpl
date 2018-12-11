<script type="text/javascript">
{literal}

var ds_bool_options = [
  {value: 0, label: 'No'},
  {value: 1, label: 'Yes'}
];

var ds_dow_options = [
  {value: 1, label: 'Monday'}, 
  {value: 2, label: 'Tuesday'},
  {value: 3, label: 'Wednesday'},
  {value: 4, label: 'Thursday'},
  {value: 5, label: 'Friday'},
  {value: 6, label: 'Saturday'},
  {value: 0, label: 'Sunday'},
  {value: '*', label: 'Daily'}  
];

$(document).ready(function() {
    editor = new $.fn.dataTable.Editor( {
        ajax: "index.php?target=ds_sites",
        table: "#sitescfg",
        fields: [ 
            {
                label: "siteid",
                name: "siteid",
                type: "readonly"
            }, 
{/literal}
{foreach from=$sites_tbl_fields item=fld name=main_fields}{if $fld.field ne 'siteid'}
           {ldelim}
                label: "{$fld.title|default:$fld.field|capitalize:true}",
{if $fld.type ne ''}
                type: "{$fld.type}",
{/if}
{if $fld.type eq "radio" && $fld.is_bool}
                options: ds_bool_options,
{/if}
{if $fld.type eq "select" && $fld.is_dow}
                options: ds_dow_options,
{/if}
                name: "{$fld.field}"
           {rdelim}{if !$smarty.foreach.main_fields.last},{/if}
{/if}{/foreach}
{literal}
        ]
    } );

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
    $('#sitescfg').on('click', 'a.editor_edit', function (e) {
        e.preventDefault();
 
        editor.edit( $(this).closest('tr'), {
            title: 'Edit record',
            buttons: 'Update'
        } );
    } );

    var ds_sites_table = $('#sitescfg').DataTable( {
        dom: "Bfrtip",
        ajax: {
            url: "index.php?target=ds_sites",
            type: "POST"
        },
        serverSide: true,
        lengthMenu: [[10], [10]],
        scrollX: true,
        order: [[ 0, "desc" ]],
        columns: [
{/literal}
{foreach from=$sites_tbl_fields item=fld name=sitescfg_main_fields}
{if !$fld.no_table_edit}

            {ldelim} data: "{$fld.field}", title: "{$fld.title|default:$fld.field|capitalize:true}", 
              render: function ( data, type, row ) {ldelim}
                var disp_field_val;
                disp_field_val = row.{$fld.field};
                {if $fld.type eq "radio" && $fld.is_bool}
                if (disp_field_val == 1) 
                    disp_field_val='Yes'; 
                else 
                    disp_field_val='No'; 
                {/if}  

                {if $fld.type eq "select" && $fld.is_dow}
                for (var s in ds_dow_options) {ldelim} 
                    if (ds_dow_options[s]['value'] == disp_field_val) 
                        disp_field_val = ds_dow_options[s]['label'];
                {rdelim}    
                {/if}

                {if $fld.title =='#'}disp_field_val = 'Edit';{/if}

                if (disp_field_val != null)
                    return '<a href="" class="editor_edit" title="Edit">' + disp_field_val + '</a>';
                else 
                    return '';
              {rdelim} 
            {rdelim},

{else}
           {ldelim} data: "{$fld.field}", title: "{$fld.title|default:$fld.field|capitalize:true}" {rdelim}{if !$smarty.foreach.sitescfg_main_fields.last},{/if}
{/if}
{/foreach}
{literal}            
        ],
        select: true, 
        buttons: [
            { extend: "create", editor: editor },
            { extend: "edit",   editor: editor },
            { extend: "remove", editor: editor },
            'pageLength'
        ]
    } );




} );
{/literal}
</script>


{include file="addons/DataScraper/admin/ds_menu.tpl" active="0"}


{capture assign='header_footer_sitescfg'}
{foreach from=$sites_tbl_fields item=fld name=sitescfg_main_fields}
<th>{$fld.title|default:$fld.field}</th>
{/foreach}
{/capture}

{capture name=section}
{capture name=block}
<table id="sitescfg" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>
{$header_footer_sitescfg}
        </tr>
    </thead>
    <tfoot>
        <tr>
{$header_footer_sitescfg}
        </tr>
    </tfoot>
</table>
<br/>
{include file='buttons/button.tpl' button_title=$lng.lbl_apply href="index.php?target=datascraper_wget_install" style='btn-green'}
<br/><br/>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block extra='width="100%"'}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_sites_scraper_settings|default:'Sites Scraper Settings'}
