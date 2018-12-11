<script type="text/javascript">
<!--
{literal}
function popup_preview_import_profile(id) {

    if ($('#preview_import_profile').length==0)
        $('body').append('<div id="preview_import_profile" style="overflow:hidden"></div>');

//    var hash = id;
//    if (hash != $('#preview_import_profile').data('hash')) {
        // Load iframe with image selector into dialog
        $('#preview_import_profile').html("<iframe frameborder='no' width='1200' height='590' src='index.php?target=flexible_import_preview&profile_id="+id+"'></iframe>");
//    }

//    $('#preview_import_profile').data('hash', hash);
    // Show dialog
    sm('preview_import_profile', 1230, 630, false, 'Preview import profile #'+id);
}
{/literal}
-->
</script>

{capture name=section}

{capture name=block}
<div class="box" id="fi_profile">
    {*if $prefilled_data.id}
        {include file="common/subheader.tpl" title=$lng.lbl_edit_profile}
    {else}
        {include file="common/subheader.tpl" title=$lng.lbl_create_profile}
    {/if*}
    <div class="box">
{if $step eq 'mapping'}

{if $prefilled_data.import_src_type eq 'T'}
  {assign var="fields_from_import" value=$prefilled_data.src_dbtable.fields.values}
{else}
  {assign var="fields_from_import" value=$prefilled_data.parsed_file.fields.values}
{/if}

<form action="index.php?target={$current_target}&mode=flexible_import_profile{if $prefilled_data.id}&profile_id={$prefilled_data.id}{/if}" method="post" name="process_profile_mapping_tables">
{if $prefilled_data.id}
     <input type="hidden" name="fi_profile[id]" value="{$prefilled_data.id}" />
{/if}
<input type="hidden" name="action" value="" />
<p>Extra tables to map on:<br /></p>
<select name="fi_profile[extra_tables][]" size="10" multiple="multiple">
{foreach from=$extra_tables item=ext_tab}
<option value="{$ext_tab}" {if in_array($ext_tab, array_keys($tmp_load_tables))}selected="selected"{/if}>{$ext_tab}</option>
{/foreach}
</select>
<br /><br />
{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('process_profile_mapping_tables', 'mapping_tables_save');" button_title="Add selected tables" style='btn-green push-20 push-5-r'}
<br />
</form>
<br />
<form action="index.php?target={$current_target}&mode=flexible_import_profile{if $prefilled_data.id}&profile_id={$prefilled_data.id}{/if}" method="post" name="process_profile_mapping">
{if $prefilled_data.id}
     <input type="hidden" name="fi_profile[id]" value="{$prefilled_data.id}" />
{/if}
<input type="hidden" name="action" value="" />
<div class="clear"></div>
<script type="text/javascript">
{literal}
$(function () {
  $('.tmp_load_vis').change(function () {                
    $('.tmp_load_'+this.value).toggle(this.checked);
    if ($(this).is(':checked')) {
        //alert('checked '+this.value);
        $(this).parent().addClass('checked');
    } else {
        //alert('not checked '+this.value); 
        $(this).parent().removeClass('checked'); 
    }
  }).change(); //ensure visible state matches initially
});
{/literal}
</script>
{assign var='map_process' value=$prefilled_data.map_process}

{foreach from=$tmp_load_tables item=tab_fields key=tab_name}
{assign var='sect_open' value=''}
{foreach from=$tab_fields item=fld_info key=fld_name}{if $fld_info.imp_field ne '' || $fld_info.custom_sql ne ''}
{assign var='preview_enabled' value=1}
{assign var='sect_open' value="checked='checked'"}{/if}
{/foreach}
<div class="col-xs-12"><label class="col-xs-12"><input type="checkbox" class="tmp_load_vis" value="{$tab_name|replace:"`":""|replace:".":"_"}" {$sect_open} />&nbsp;{$tab_name}</label></div>
{/foreach}
<table class="table table-striped dataTable header" width="100%">
<tr><th width="50%">Destination</th><th width="50%">Source</th></tr>
{foreach from=$tmp_load_tables item=tab_fields key=tab_name}
{foreach from=$tab_fields item=fld_info key=fld_name}
<tr class="tmp_load_{$tab_name|replace:"`":""|replace:".":"_"}" style="display:none">
<td colspan="2" align="right">
<div class="form-group">
<label {if $fld_info.key}style="font-weight: bold" title="Key field"{/if}>{$tab_name}.{$fld_name}</label>

{if !in_array($tab_name, array_keys($core_tmp_load_tables))}update Key:&nbsp;<input type="checkbox" value="1" name="mapping[{$tab_name}][{$fld_name}][is_update_key]" {if $fld_info.is_update_key}checked="checked"{/if}/>&nbsp;{/if}
<select name="mapping[{$tab_name}][{$fld_name}][imp_field]" style="width:45%">
<option name=""></option>
{foreach from=$fields_from_import item=imp_field}
  <option value="{$imp_field}" {if $fld_info.imp_field eq $imp_field}selected="selected"{/if}>{$imp_field}</option>
{/foreach}
</select>
</div>
<div class="form-group">
<label>SQL</label>
<textarea rows="5" name="mapping[{$tab_name}][{$fld_name}][custom_sql]" style="width:80%;font-size:12px;">{$fld_info.custom_sql}</textarea>
</div>
{*
<div class="input_field_0">
<label>PHP</label>
<textarea name="mapping[{$tab_name}][{$fld_name}][custom_php]" style="width:70%">{$fld_info.custom_php}</textarea>
</div>
*}
</td>
</tr>
{/foreach}
<tr class="tmp_load_{$tab_name|replace:"`":""|replace:".":"_"}"><td colspan="2" align="right">
Clean {$tab_name} before copy new data:&nbsp;<label><input style="float:right; margin-top:0px" type="checkbox" value="1" name="map_process[{$tab_name}][clean_table]" {if $map_process.$tab_name.clean_table eq 1}checked="checked"{/if}/></label><br />
<b>Clean table condition:</b>
<textarea rows="2" style="width:95%; font-size:12px;" name="map_process[{$tab_name}][clean_table_condition]">{$map_process.$tab_name.clean_table_condition}</textarea>
</td></tr>
<tr class="tmp_load_{$tab_name|replace:"`":""|replace:".":"_"}"><td colspan="2" align="right">
<label>SQL to execute after loading of parsed data to <b>{tunnel func='cw_flexible_import_add_prefix' via='cw_call' param1='tmp_load_' param2=$tab_name}</b> table</label><br />
<textarea rows="10" style="width:95%; font-size:12px;" name="map_process[{$tab_name}][post_sql]">{$map_process.$tab_name.post_sql}</textarea>
</td></tr>
{/foreach}
</table>
<div class="buttons">{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('process_profile_mapping', 'mapping_save');" button_title=$lng.lbl_update style='btn-green push-20 push-5-r'}
{if $prefilled_data.id && $preview_enabled}
&nbsp;{include file='admin/buttons/button.tpl' href="javascript: popup_preview_import_profile(`$prefilled_data.id`);" button_title=$lng.lbl_preview|default:'Preview' style='btn-green push-20 push-5-r'}
{/if}</div>
</form>

{else}
<script type="text/javascript">
{literal}
function update_recurring_import_divs() {
  if ($("#active_recurring_ctrl").is(":checked")) {
//     $("#recurring_import_path_div").removeClass('disabled'); 
     $("#recurring_import_period_div").removeClass('disabled');
  } else {
//     $("#recurring_import_path_div").addClass('disabled'); 
     $("#recurring_import_period_div").addClass('disabled');
  }
}

function flexible_import_update_import_src_type() {
    switch ($('#import_src_type').val()) {
        case '':
        $('.file_src_option').show();
        $('.dbtable_src_option').hide();
        break;
        case 'T':
        $('.file_src_option').hide();
        $('.dbtable_src_option').show();
        break;
    }
}

$(document).ready(function() {
  $('#import_src_type').on('change', flexible_import_update_import_src_type);
  flexible_import_update_import_src_type(); 
});

{/literal}
</script>

        <form enctype="multipart/form-data" action="index.php?target={$current_target}&mode=flexible_import_profile{if $prefilled_data.id}&profile_id={$prefilled_data.id}{/if}" method="post" name="process_profile">
            {if $prefilled_data.id}
            <input type="hidden" name="fi_profile[id]" value="{$prefilled_data.id}" />
            {/if}
            <input type="hidden" name="action" value="" />
        <div class="form-group">
            <label class='required col-xs-12'>{$lng.lbl_name}</label>
            <div class="col-xs-12">
                <input type="text"  name="fi_profile[name]" value="{$prefilled_data.name}"/>
            </div> 
        </div>
        <div class="form-group">
            <label class='col-xs-12'>{$lng.lbl_description}</label>
            <div class="col-xs-12">
                <textarea name="fi_profile[description]" value="{$prefilled_data.description}" rows="2" cols="10" style="width: 500px;">{$prefilled_data.description}</textarea>
            </div>
        </div>
        <div class="form-group">   
            <label class='col-xs-12'>{$lng.lbl_recurring_options|default:'Recurring options'}:</label>
            <div class="col-xs-12">
              <input type="checkbox" name="fi_profile[active_reccuring]" {if $prefilled_data.active_reccuring}checked="checked"{/if} id="active_recurring_ctrl"  onclick="javascript: update_recurring_import_divs();" value="1" />&nbsp;{$lng.lbl_active_reccuring_task|default:'Active recurring task'}
            </div>
       </div>   
        <div class="form-group">
            <label class='col-xs-12'>{$lng.lbl_import_from|default:'Import from'}</label>
            <div class="col-xs-12">
                <select name="fi_profile[import_src_type]" id="import_src_type">
                   <option value="">{$lng.lbl_file}</option>
                   <option value="T" {if $prefilled_data.import_src_type eq "T"}selected="selected"{/if}>{$lng.lbl_database_table|default:'Database table'}</option>
                </select>
            </div> 
       </div>
       <div class="form-group dbtable_src_option">
            <label class="col-xs-12">{$lng.lbl_database_table|default:'Database table'}</label>
            <div class="col-xs-12"> 
                <select name="fi_profile[dbtable_src]">
                  {foreach from=$extra_tables item=dbtable} 
                  <option value="{$dbtable}" {if $prefilled_data.dbtable_src eq $dbtable}selected="selected"{/if}>{$dbtable}</option>
                  {/foreach}
                </select>
            </div> 
       </div> 
       <div class="form-group file_src_option" id="recurring_import_path_div">
            <label class="col-xs-12">{$lng.lbl_filepath_or_url|default:'Full Path to Server or URL'}</label>
            <div class="col-xs-12">
                <input type="text" size="100" name="fi_profile[recurring_import_path]" value="{$prefilled_data.recurring_import_path}"/>
            </div>
       </div>
       <div class="form-group{if !$prefilled_data.active_reccuring} disabled{/if} file_src_option" id="recurring_import_period_div">
            <label class="col-xs-12">{$lng.lbl_import_period|default:'Run import every'}</label> 
            <div class="col-xs-12">
            <select name="fi_profile[recurring_import_days]">
            {section name=days start=0 loop=30 step=1}
                <option value="{$smarty.section.days.index}" {if $prefilled_data.recurring_import_days eq $smarty.section.days.index}selected{/if}>{$smarty.section.days.index}</option>
            {/section}
            </select>&nbsp;{$lng.lbl_days}&nbsp;&nbsp;
            <select name="fi_profile[recurring_import_hours]">
            {section name=hrs start=0 loop=24 step=1}
                <option value="{$smarty.section.hrs.index}" {if $prefilled_data.recurring_import_hours eq $smarty.section.hrs.index}selected{/if}>{$smarty.section.hrs.index}</option>
            {/section}
            </select>&nbsp;{$lng.lbl_hours|default:'hours'}
            </div>
       </div>

        <h3 class='file_src_option'>{$lng.lbl_parsing_options}:</h3>

        <div class="form-group file_src_option">
             <label class="col-xs-12">{$lng.lbl_standard_csv_options|default:'Standard CSV options (file delimiter)'}:</label>
             <div class="col-xs-12"><input type='radio' name='fi_profile[type]' value='tab' {if $prefilled_data.type eq 'tab'}checked="cheched" {/if} />&nbsp;{$lng.lbl_tab|default:'Tab'}
             <input type='radio' name='fi_profile[type]' value='comma' {if $prefilled_data.type eq 'comma' || $prefilled_data.type eq ''}checked="cheched" {/if} />&nbsp;{$lng.lbl_comma|default:'Comma'}
             <input type='radio' name='fi_profile[type]' value='semicolon' {if $prefilled_data.type eq 'semicolon'}checked="cheched" {/if} />&nbsp;{$lng.lbl_semicolon|default:'Semicolon'}</div>
        </div>

        <div class="form-group fi_options {if $prefilled_data.type !='custom'}disabled{/if} file_src_option" id="fi_custom">
            <label class="col-xs-12"><input type='radio' name='fi_profile[type]' value='custom' {if $prefilled_data.type eq 'custom'}checked="cheched" {/if} />&nbsp;{$lng.lbl_custom_options}:</label>
            <div class="col-xs-12">
            <table style="width: 90%;" class="table table-striped dataTable header_bordered">
                <tr>
                    <th>{$lng.lbl_delimiter}</th>
                    <th>{$lng.lbl_lines_terminate}</th>
                    <th>{$lng.lbl_enclose_char}</th>
                    <th>{$lng.lbl_escape_char}</th>
                </tr>
                <tr>
                    <td><input type="text" value="{if $prefilled_data.custom ne ''}{$prefilled_data.custom.delimiter}{/if}" name="fi_profile[custom][delimiter]" maxlength="2"></td>
                    <td><input type="text" value="{if $prefilled_data.custom ne ''}{$prefilled_data.custom.lines}{/if}" name="fi_profile[custom][lines]"></td>
                    <td><input type="text" value="{if $prefilled_data.custom ne ''}{$prefilled_data.custom.enclosure}{/if}" name="fi_profile[custom][enclosure]" maxlength="1"></td>
                    <td><input type="text" value="{if $prefilled_data.custom ne ''}{$prefilled_data.custom.escape}{/if}" name="fi_profile[custom][escape]" maxlength="2"></td>
                </tr>
            </table>
            </div>
        </div>

        <div class="form-group fi_options  {if $prefilled_data.type !='advanced'}disabled{/if} file_src_option" id="fi_advanced">
            <label class="col-xs-12"><input type='radio' name='fi_profile[type]' value='advanced' {if $prefilled_data.type eq 'advanced'}checked="cheched" {/if}/>&nbsp;{$lng.lbl_advanced_file_options}:</label>
            <div class="col-xs-12">
            <table style="width: 90%;" class="table table-striped dataTable header_bordered">
                <tr>
                    <th>{$lng.lbl_number_of_columns}</th>
                    <th>{$lng.lbl_chars_to_trim}</th>
                    <th>{$lng.lbl_enclose_char}</th>
                    <th>{$lng.lbl_escape_char}</th>
                </tr>
                <tr>
                    <td>
                        <select class="required" id="fi_adv_num_columns" onchange="add_column_fileds(this.options[this.selectedIndex].value)" name="fi_profile[adv][num_columns]"  width="40">
                            {section name=num start=1 loop=41 step=1}
                                <option value="{$smarty.section.num.index}" {if $prefilled_data.adv.num_columns == $smarty.section.num.index}selected="selected"{/if}>{$smarty.section.num.index}</option>
                            {/section}
                        </select>
                    </td>
                    <td><input type="text" value="{$prefilled_data.adv.chars_to_trim}" name="fi_profile[adv][chars_to_trim]"></td>
                    <td><input type="text" value="{$prefilled_data.adv.enclose_char}" name="fi_profile[adv][enclose_char]" maxlength="1"></td>
                    <td><input type="text" value="{$prefilled_data.adv.escape_char}" name="fi_profile[adv][escape_char]" maxlength="2"></td>
                </tr>
            </table>
            </div>
            <div class="col-xs-12">
            <table style="width: 90%;" class="table table-striped dataTable header_bordered" id="fi_adv_columns_table">
                <tr>
                    <th width="5%">{$lng.lbl_column_num}:</th>
                    <th style="width:80px">{$lng.lbl_mandatory}</th>
                    <th>{$lng.lbl_delimiter}</th>
                    <th>{$lng.lbl_column_type}</th>
                    <th style="width:160px">{$lng.lbl_field_name}</th>
                </tr>
                {if $prefilled_data.adv.column[1]}
                        {foreach from=$prefilled_data.adv.column item=column name=row}
                            <tr id="fi_adv_column_{$smarty.foreach.row.iteration}" class="fi_adv_column">
                                <td>{$smarty.foreach.row.iteration}</td>
                                <td><input type="checkbox" class="fi_mandatory" name="fi_profile[adv][column][{$smarty.foreach.row.iteration}][mandatory]" value="1" {if $column.mandatory}checked="checked"{/if} /></td>
                                <td><input type="text" class="adv_field_delimiter" value="{$column.delimiter}" name="fi_profile[adv][column][{$smarty.foreach.row.iteration}][delimiter]" maxlength="2"></td>
                                <td>
                                    <select class="required" name="fi_profile[adv][column][{$smarty.foreach.row.iteration}][column_type]" width="40">
                                        <option value="">{$lng.lbl_please_select}</option>
                                        <option value="free" {if $column.column_type eq 'free'} selected="selected"{/if}>{$lng.lbl_free}</option>
                                        <option value="text" {if $column.column_type eq 'text'} selected="selected"{/if}>{$lng.lbl_text}</option>
                                        <option value="numeric" {if $column.column_type eq 'numeric'} selected="selected"{/if}>{$lng.lbl_numeric}</option>
                                    </select>
                                </td>
                                <td>
                                <input type="text" class="adv_field_name" onblur="if(this.value=='')this.value='field_1'" onfocus="if(this.value=='field_1')this.value=''" value="{if $column.field_name}{$column.field_name}{else}field_{$smarty.foreach.row.iteration}{/if}" name="fi_profile[adv][column][{$smarty.foreach.row.iteration}][field_name]" maxlength="32">
                                </td>

                            </tr>
                        {/foreach}

                    {else}
                <tr id="fi_adv_column_1" class="fi_adv_column">
                    <td>1</td>
                    <td><input type="checkbox" class="fi_adv_mandatory" name="fi_profile[adv][column][1][mandatory]" value="1" /></td>
                    <td><input type="text" class="fi_adv_delimiter" value="" name="fi_profile[adv][column][1][delimiter]" maxlength="2"></td>
                    <td>
                        <select class="required" name="fi_profile[adv][column][1][column_type]" width="40">
                            <option value="">{$lng.lbl_please_select}</option>
                            <option value="free">{$lng.lbl_free}</option>
                            <option value="text">{$lng.lbl_text}</option>
                            <option value="numeric">{$lng.lbl_numeric}</option>
                        </select>
                    </td>  
                    <td>
                        {literal}
                        <input type="text" class="adv_field_name" onblur="if(this.value==''){this.value='field_1'}" onfocus="if(this.value=='field_1')this.value=''" value="field_1" name="fi_profile[adv][column][1][field_name]" maxlength="32"></td>
                        {/literal}
                    </td>
                </tr>
                {/if}
            </table>
            </div>
        </div>
        <br>
        <h3 class='file_src_option'>{$lng.lbl_additional_parser_params}:</h3>
        <div class="col-xs-12 file_src_option">
            <table style="width: 90%;" class="table table-striped dataTable header_bordered">
                <tr>
                    <th>{$lng.lbl_lines_to_skip}</th>
                    {*<th>Column name line row</th>*}
                    <th>{$lng.lbl_column_names_line_id}</th>
                    <th style="width:30%;">{$lng.lbl_use_values_from_single_column}</th>
                    <th style="width:30%;">{$lng.lbl_example_file}</th>
                </tr>
                <tr>
                    <td><input type="text" value="{$prefilled_data.num_lines_to_skip}" name="fi_profile[num_lines_to_skip]" maxlength="7"></td>
                    <td><input type="text" value="{if $prefilled_data.col_names_line_id eq 'n'}{elseif $prefilled_data.col_names_line_id}{$prefilled_data.col_names_line_id}{else}1{/if}" name="fi_profile[col_names_line_id]"></td>
                    <td><input type="checkbox" class="fi_mandatory" name="fi_profile[use_category_column]" {if $prefilled_data.use_category_column}checked="checked"{/if} value="1" /></td>
                    <td><input type="file" name="import_file" id="fi_test_file"></td>
                </tr>
            </table>
        </div>

        <div class="file_src_option" id="pre_parsed_file" {if $prefilled_data.test_file_demo_content}style="display:block" {/if}>
            <br>
            <h3>{*$lng.lbl_file_first_lines*}</h3>
            <input type="hidden" id="test_file_demo_content" name="fi_profile[test_file_demo_content]" value="{$prefilled_data.test_file_demo_content}" />
            <div class="col-xs-12">
                <div id="row_test_file">{$prefilled_data.test_file_demo_content}</div>
            </div>
        </div>

    {if $prefilled_data.parsed_file}
        <div id="parsed_file">
            <h3>Parsed file (first 30 lines){*$lng.lbl_parsed_file_lines*}</h3>
            <div>{$lng.lbl_test_file|default:'Test file'}: {$prefilled_data.parsed_file.source_file_name}&nbsp;<br />
                       {if $prefilled_data.sheets}Select sheet:&nbsp;<select name="fi_profile[selected_sheet]">{foreach from=$prefilled_data.sheets item=sh}<option value="{$sh}" {if $prefilled_data.selected_sheet eq $sh}selected="selected"{/if}>{$sh}</option>{/foreach}</select>{/if}
            </div>

        {if $prefilled_data.parsed_file.err}
                    {*TODO: Add error messages*}
        {elseif $prefilled_data.parsed_file.fields}
            <div id="test_parsed_file" style="overflow: scroll">

                <table  style="width: auto;" class="table table-striped dataTable header_bordered">

                    {foreach from=$prefilled_data.parsed_file.fields.values item=field}
                        <th>{$field}</th>
                    {/foreach}
                    {foreach from=$prefilled_data.parsed_file.data item=record}
                        <tr>
                        {foreach from=$record item=val}
                            <td>{$val|base64_decode}</td>
                        {/foreach}
                        </tr>
                    {/foreach}
                </table>

            </div>
        {else}
            <div>No content</div>
        {/if}
        </div>
    {elseif $prefilled_data.import_src_type eq 'T'}
        <div id="src_dbtable">
            <h3>Source table (first 30 entries)</h3>
            <div>{$prefilled_data.dbtable_src}&nbsp;<br /></div>
            <div>
                <table style="width: auto;" class="table table-striped dataTable header_bordered">
                    <tr>
                    {foreach from=$prefilled_data.src_dbtable.fields.values item=field}
                        <th>{$field}</th>
                    {/foreach}
                    </tr> 
                    {foreach from=$prefilled_data.src_dbtable.data item=record}
                        <tr>
                        {foreach from=$record item=val}
                            <td>{$val}</td>
                        {/foreach}
                        </tr>
                    {/foreach}
                </table>
             </div>  
         </div>
    {/if}
    <br>
    <div class="button_left_align buttons">
        {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('process_profile', 'test_profile');" button_title=$lng.lbl_test_profile style='btn-green push-20 push-5-r'}

{*if $prefilled_data.id}
{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('process_profile', 'import_file');" button_title=$lng.lbl_import }
{/if*}

        {if ($prefilled_data.parsed_file.fields || $prefilled_data.id) || ($prefilled_data.import_src_type eq 'T' && $prefilled_data.dbtable_src ne '')}
            {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('process_profile', 'save_profile');" button_title=$lng.lbl_save_switch_column_layout style='btn-green push-20 push-5-r'}
        {/if}
    </div>  
        <div class="clear"></div>
    </form>
    {/if}
  </div>
</div>
{/capture}

{if $prefilled_data.id}
    {include file="admin/wrappers/block.tpl" content=$smarty.capture.block extra='width="100%"' title=$lng.lbl_edit_profile}
{else}
    {include file="admin/wrappers/block.tpl" content=$smarty.capture.block extra='width="100%"' title=$lng.lbl_create_profile}
{/if}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_flexible_import}

