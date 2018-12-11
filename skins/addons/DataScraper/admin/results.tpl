<script type="text/javascript">
{literal}

var ds_bool_options = [
  {value: 0, label: 'No'},
  {value: 1, label: 'Yes'}
];

$(document).ready(function() {
    editor = new $.fn.dataTable.Editor( {
        ajax: "index.php?target=ds_results",
        table: "#resultscfg",
        fields: [ 
            {
                label: "result_id",
                name: "result_id",
                type: "readonly"
            }, 
{/literal}
{foreach from=$results_tbl_fields item=fld name=main_fields}{if $fld.field ne 'result_id'}
           {ldelim}
                label: "{$fld.title|default:$fld.field|capitalize:true}",
{if $fld.type ne ''}
                type: "{$fld.type}",
{/if}
{if $fld.type eq "radio" && $fld.is_bool}
                options: ds_bool_options,
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
    $('#resultscfg').on('click', 'a.editor_edit', function (e) {
        e.preventDefault();
 
        editor.edit( $(this).closest('tr'), {
            title: 'Edit record',
            buttons: 'Update'
        } );
    } );

    var ds_results_table = $('#resultscfg').DataTable( {
        dom: "Bfrtip",
        ajax: {
            url: "index.php?target=ds_results",
            type: "POST"
        },
        serverSide: true,
        lengthMenu: [[20, 50, 100, 500, 1000], [20, 50, 100, 500, 1000]],
        scrollX: true,
        order: [[ 0, "desc" ]],
        columns: [
{/literal}
{foreach from=$results_tbl_fields item=fld name=resultscfg_main_fields}
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

                {if $fld.title =='#'}disp_field_val = 'Edit';{/if}

                if (disp_field_val != null)
                    return '<a href="" class="editor_edit" title="Edit">' + disp_field_val + '</a>';
                else 
                    return '';
              {rdelim} 
            {rdelim},

{else}
           {ldelim} data: "{$fld.field}", title: "{$fld.title|default:$fld.field|capitalize:true}" {rdelim}{if !$smarty.foreach.resultscfg_main_fields.last},{/if}
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


{include file="addons/DataScraper/admin/ds_menu.tpl" active="2"}


{capture assign='header_footer_resultscfg'}
{foreach from=$results_tbl_fields item=fld name=resultscfg_main_fields}
<th>{$fld.title|default:$fld.field}</th>
{/foreach}
{/capture}

{capture name=section}
{capture name=block}
<form method="GET" action="index.php" name="results_site_sel">
<input type="hidden" name="target" value="datascraper_results" />
<select name="_site_id" onchange="javascript: cw_submit_form('results_site_sel');">
{foreach from=$ds_sites item=s key=sid}
<option value="{$sid}" {if $curr_site_id eq $sid}selected{/if}>{$s.name}</option>
{/foreach}
</select>
</form>
<p/>

<table id="resultscfg" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>
{$header_footer_resultscfg}
        </tr>
    </thead>
    <tfoot>
        <tr>
{$header_footer_resultscfg}
        </tr>
    </tfoot>
</table>
<br/>
{if $curr_site_results_count|default:0 gt 0}
{include file='buttons/button.tpl' button_title=$lng.lbl_remove_results|default:"Remove scraped results for current site" href="javascript: if (confirm('All scraped items from selected site will be deleted!')) location.href='index.php?target=datascraper_results_clean'" style='btn-green'}
&nbsp;&nbsp;
{include file='buttons/button.tpl' button_title=$lng.lbl_reset_parse_flag|default:"Reset parse flag for current site" href="javascript: if (confirm('This will allow the parse script to parse downloaded pages from the beginning and to update or filll results table once again.')) location.href='index.php?target=datascraper_parse_reset'" style='btn-green'}
&nbsp;&nbsp;
{include file='buttons/button.tpl' button_title=$lng.lbl_load_to_datahub|default:"Load to datahub" href="javascript: if (confirm('All scraped items from selected site will be loaded to datahub import buffer')) location.href='index.php?target=datascraper_results_load'" style='btn-green'}
{/if}
<br /><br />
{include file='buttons/button.tpl' button_title=$lng.lbl_parse_downloaded_data|default:"Start/continue parsing of downloaded data" href="javascript: if (confirm('This will update or fill scraped results tables on all sites with enabled parsing and downloaded data.')) location.href='../index.php?target=datascraper_parse&webrestart=Y'" style='btn-green'}
<br/><br/>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block extra='width="100%"'}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_scraper_results|default:'Scraped Sites Results'}
