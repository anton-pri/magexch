{capture name=section}
{capture name=block}

<form action="index.php?target={$current_target}&mode=expdata" method="post" name="export_form" enctype="multipart/form-data">
<input type="hidden" name="action" value="export">

<div class="Error" style="color:#4e0202;">{$err_msg}</div>

<div class="form-horizontal">
{*include file="common/subheader.tpl" title=$lng.lbl_exp_data_csv*}
<div class="form-group">
    
<table class="table table-hover table-condensed">
<thead>
 <tr>
     <th width='50'>{$lng.lbl_export}</th>
     <th>Table</th>
     <th>{$lng.lbl_saved_search}</th>
 </tr>
</thead>
<tbody>
        
{section name=ind loop=$export_list}
{assign var='tbl' value=$export_list[ind]}
<tr>
	<td align='center'>
        <input type="checkbox" name="export_table[{$export_list[ind]}]" value="1" />&nbsp;
    </td>
    <td>
        {$export_list[ind]}
    </td>
    <td>
        {if $saved_searches.$tbl}
          <select class="form-control" name="preset[{$tbl}]" title="{$lng.lbl_load_saved_search|default:'Load saved search'}">
            <option value="">{$lng.lbl_all}</option>
            {foreach from=$saved_searches.$tbl item=ssi}
            <option value="{$ssi.ss_id}">{$ssi.name|stripslashes}</option>
            {/foreach}
          </select>        
        {/if}
    </td>
</tr>
{/section}
</tbody>
</table>
</div> <!-- class="form-group" -->

<div class="form-group">
    <label class="col-xs-12">CSV delimiter:</label>
    <div class="col-xs-2">
    <select name="delimiter" class="form-control">
  		<option value=";">Semicolon</option>
  		<option value=",">Comma</option>
  		<option value="tab">Tab</option>
    </select>
    </div>
</div> <!-- class="form-group" -->


</div> <!-- class="form-horizontal" -->

<div class="buttons">
    {include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('export_form');" button_title=$lng.lbl_export_data acl='__1300' style="btn-green push-20"}
</div>
</form>

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{if $files}
{capture name=section2}
<form action="index.php?target={$current_target}&mode=expdata" method="post" name="delete_form" enctype="multipart/form-data">
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
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_exp_data_csv}
