{capture name=section}
{capture name=block}

<form action="index.php?target={$current_target}&mode=do" method="post" name="export_form" enctype="multipart/form-data">
<input type="hidden" name="action" value="export">

<div class="form-horizontal">
<div class="form-group">
    
<table class="table table-hover table-condensed">
<thead>
 <tr>
     <th width='50'>{$lng.lbl_export}</th>
     <th>Type</th>
     <th>Schema</th>
     <th>{$lng.lbl_saved_search}</th>
 </tr>
</thead>
<tbody>
        
{foreach from=$export_types key=type_code item=export_type name='exports'}
{assign var='ii' value=$smarty.foreach.exports.iteration}
<input type="hidden" name="types[{$ii}][type]" value="{$type_code}" />
<tr>
	<td align='center'>
        <input type="checkbox" name="types[{$ii}][export]" value="1" />&nbsp;
    </td>
    <td>
        {$export_type.name}
    </td>
    <td>
        <select class="form-control" name="types[{$ii}][schema]" title="{$lng.lbl_schema|default:'Schema'}">
        {foreach from=$export_type.schemas key=schema_code item=schema}
            <option value='{$schema_code}'>{$schema.name}</option>
        {/foreach}
        </select>
    </td>
    <td>
        {assign var='saved_search_type' value=$export_type.saved_search}
        {if $saved_searches.$saved_search_type}
          <select class="form-control" name="types[{$ii}][saved_search]" title="{$lng.lbl_load_saved_search|default:'Load saved search'}">
            {foreach from=$saved_searches[$saved_search_type].presets item=ssi}
            <option value="{$ssi.ss_id}">{$ssi.name|stripslashes}</option>
            {/foreach}
            <option value="">{$lng.lbl_all}</option>
          </select>        
        {/if}
    </td>
</tr>
{/foreach}
</tbody>
</table>
</div> <!-- class="form-group" -->

<div class='row'>
<div class="form-group col-lg-4">
    <label class="col-xs-12">Format:</label>
    <div class="col-xs-12">
    <select name="format" class="form-control">
        <option value="">Auto</option>
    {foreach from=$export_formats item=format key=format_code}
  		<option value="{$format_code}">{$format.name}</option>
    {/foreach}

    </select>
    </div>
</div> <!-- class="form-group" -->

<div class="form-group col-lg-4">
    <label class="col-xs-12">Delimiter:</label>
    <div class="col-xs-12">
    <select name="delimiter" class="form-control">
        <option value="">Auto</option>
  		<option value=";">Semicolon</option>
  		<option value=",">Comma</option>
  		<option value="tab">Tab</option>
    </select>
    </div>
</div> <!-- class="form-group" -->
</div>

</div> <!-- class="form-horizontal" -->

<div class="buttons">
    {include file='admin/buttons/button.tpl' href="javascript: cw_export_start();" button_title=$lng.lbl_export_data acl='__1300' style="btn-green push-20"}
</div>
</form>

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{if $files}
{capture name=section2}
<form action="index.php?target={$current_target}&mode=do" method="post" name="delete_form" enctype="multipart/form-data">
<input type="hidden" name="action" value="delete">

<div class="form-horizontal">
{assign var="i" value=0}
<div class="form-group">
{section name=ind loop=$files}
<div class="col-xs-12">
	<div class="checkbox">
		<label>
			<input type="checkbox" name="filenames[{$i++}]" value="{$files[ind]}" />&nbsp;
			<a href="{$path}/{$files[ind]}">{$files[ind]}</a>
		</label>
	</div>
</div>
{/section}
</div>
</div>

<div class="buttons">{include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('delete_form');" button_title=$lng.lbl_delete_selected acl='__1300' style="btn-danger push-20"}</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.section2 title=$lng.lbl_export_packs}
{/if}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title="Export data to file"}
<div id='export_log' style='font-size: 13px; font-family:sans-serif;'></div>
<script>
{literal}
var statusFunc;
function cw_export_start() {
    $('#export_log').html('');
    sm('export_log',800,600,null,'Export log');
    submitFormAjax('export_form',function(){window.clearInterval(statusFunc)});
    statusFunc = window.setInterval(get_export_log,5000);
}

function get_export_log() {
    ajaxGet('index.php?target=export&mode=status');
}
{/literal}
</script>
