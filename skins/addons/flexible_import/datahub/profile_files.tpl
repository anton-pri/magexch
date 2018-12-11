{include file="addons/flexible_import/flexible_import_menu.tpl"}

{capture name=section}
{capture name=block1}

{if $file_names ne ''}
<form name="profile_files_form" method="get" action="index.php?target={$current_target}">
<input type="hidden" name="target" value="{$current_target}">
<input type="hidden" name="action" value="" />
<input type="hidden" name="profile_id" value="{$fi_profile.id}" />
<input type="hidden" name="hash2reload" value="" />
<table width='100%' cellpadding='3' cellspacing='0' class="table table-striped">
<thead>
  <tr>
    <th>Date Copied To Server</th> 
    {*<th>Date Modified</th>*}
    <th>File Name</th>  
    <th>Date Loaded</th>
    <th>Reload</th>               
  </tr>
</thead>
{foreach from=$file_names item=fn}
<tr>
    <td>{$fn.modified|date_format:$config.Appearance.datetime_format}</td>
    {*<td>{$fn.modified|date_format:$config.Appearance.datetime_format}</td>*}
    <td title="{$fn.full_filename}">{$fn.filename}</td>
    <td>{if $fn.hash_info ne ''}{$fn.hash_info.date_loaded|date_format:$config.Appearance.datetime_format}{else}never{/if}</td>
    <td>{if $fn.hash_info ne ''}{assign var='hash2reload' value=$fn.hash_info.hash}{include file='buttons/button.tpl' button_title=$lng.lbl_refresh|default:'Reload file' href="javascript: document.profile_files_form.hash2reload.value='$hash2reload'; if(confirm('Delete earlier saved hash of selected file and make it load to datahub import buffer once again?')) cw_submit_form('profile_files_form', 'reload');" style='btn-green'}{/if}</td> 
</tr>
{/foreach}
</table>
<div>
<br><br>
</div>
</form>
{else}
<form name="profile_files_form" method="get" action="index.php?target={$current_target}">
<input type="hidden" name="target" value="{$current_target}">
<input type="hidden" name="action" value="" />
<input type="hidden" name="profile_id" value="{$fi_profile.id}" />
<input type="hidden" name="date_loaded" value="" />
<h4>No profile files found.</h4><br><br>
</form>
{/if}
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block1 title=$fi_profile.name}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_flexible_import_profile_files|default:'Flexible import profile files'}
