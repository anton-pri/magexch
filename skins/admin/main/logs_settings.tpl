{capture name=section}
{capture name=block1}

{if $registered_logs ne ''}
<form name="logs_settings_form" method="post" action="index.php?target=logs_settings">
<input type="hidden" name="target" value="logs_settings">
<input type="hidden" name="action" value="" />
<table width='100%' cellpadding='3' cellspacing='0' class="table table-striped">
<thead>
  <tr>
    <th>Select</th>
    <th>Name</th> 
    <th>Active</th>
    <th>Max Days to Keep</th>  
    <th>Action after</th>
    <th>Size</th>               
    <th>Email Notification</th>
    <th>Email Unique Log Once A Day</th>
  </tr>
</thead>
{foreach from=$registered_logs key=logname item=fn}
<tr>
    <td><input type='checkbox' name="posted_settings[{$logname}][delete_files]" value="1" /></td>
    <td>{$logname}</td>
    <td><input type='checkbox' name="posted_settings[{$logname}][active]" value="1" {if $fn.settings.active eq 1}checked="checked"{/if}/></td>
    <td>
        <select name="posted_settings[{$logname}][max_days]">
           <option value="0" {if $fn.settings.max_days eq 0}selected{/if}>Always</option> 
          {section name=days_cnt start=1 loop=60 step=1}
           <option value="{$smarty.section.days_cnt.index}" {if $fn.settings.max_days eq $smarty.section.days_cnt.index}selected{/if}>{$smarty.section.days_cnt.index}</option>
          {/section}
        </select>
    </td>
    <td>
        <select name="posted_settings[{$logname}][action_after_max_days]">
             <option value="D" {if $fn.settings.action_after_max_days eq 'D'}selected{/if}>Delete</option>
             <option value="A" {if $fn.settings.action_after_max_days eq 'A'}selected{/if}>Archive</option>
        </select>
    </td>
    <td nowrap>{$fn.size}<br> in <b>{$fn.files_count}</b> file(s)</td>
    <td><input type='checkbox' name="posted_settings[{$logname}][email_notify]" value="1" {if $fn.settings.email_notify eq 1}checked="checked"{/if}/></td>
    <td><input type='checkbox' name="posted_settings[{$logname}][email_notify_once]" value="1" {if $fn.settings.email_notify_once eq 1}checked="checked"{/if}/></td>
</tr>
{/foreach}
</table>
<div>
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('logs_settings_form');" style="btn-green"}
&nbsp;
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected_log_files|default:'Delete Selected Log Files' href="javascript: if(confirm('Delete Selected Files?')) cw_submit_form('logs_settings_form','delete');" style="btn-green"}
&nbsp;
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected_log|default:'Delete Selected Log' href="javascript: if(confirm('Delete Selected Log Totally (Both Files and Settings)?')) cw_submit_form('logs_settings_form','delete_totally');" style="btn-green"}
<br><br>
<br><br>
</div>
</form>
{else}
<h4>No log files found.</h4><br><br>
{/if}
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block1 title="Summary Logs Size: `$summary_size`"}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_logs_settings|default:'Logs Settings'}
