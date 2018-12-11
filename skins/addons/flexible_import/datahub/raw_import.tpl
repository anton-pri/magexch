{include file="addons/flexible_import/flexible_import_menu.tpl" active="0"}
{capture name=section}

<script type="text/javascript">
{literal}
$(document).ready(function() {
/*
    $('#fi_profiles_tbl').fixheadertable({
        caption        : 'Data Hub Import Profiles',
        height         : 300,
        sortable       : 1,
        resizeCol      : true,
        showhide       : 0,
        minColWidth    : 180,
        addTitles      : true,
        whiteSpace     : 'normal'
    });
    $('#log_entries_tbl').fixheadertable({
        caption        : 'Logged Import Events',
        height         : 300,
        sortable       : 1,
//        resizeCol      : true,
        showhide       : 0,
//        minColWidth    : 150,
        addTitles      : true,
        whiteSpace     : 'normal'
    });
*/
});
{/literal}
</script>

{capture assign='dh_main_display_title'}{$lng.lbl_datahub_current_count_of_buffer_items|default:'Current count of items in import hub buffer table'}: {$buffer_lines_count|default:0}<br>{$lng.lbl_datahub_current_count_of_interim_buffer_items|default:'Current count of items in interim import hub buffer table'}: {$interim_buffer_lines_count|default:0}{/capture}

{capture name=block1}
<table id="fi_profiles_tbl" width='100%' cellpadding='3' cellspacing='0' height="200px" class="table table-striped">
<thead>
<tr>
<th>{$lng.lbl_name}</th>
<th>{$lng.lbl_active_reccuring_task|default:'Automatically load new files'}</th>
<th>{$lng.lbl_filepath_or_url|default:'Full Path to Server or URL'}</th>
{*<th>{$lng.lbl_import_period|default:'Run import every'}</th>*}
<th>{$lng.lbl_recurring_last_run_date|default:'Last time run'}</th>
{*<th>{$lng.lbl_edit}</th>*}
</tr>
</thead>
{foreach from=$datahub_fi_profiles item=profile}<tr>
<td nowrap>
{if $profile.description ne ''}
  {capture assign='profile_name'}{strip}
    <span class="lng_tooltip" title="Description: {$profile.description|escape:'html'}">{$profile.name}</span>
  {/strip}{/capture}
{else}
  {*assign var='profile_name' value=$profile.name*}
  {capture assign='profile_name'}{strip}
    <span class="lng_tooltip" title="No Description">{$profile.name}</span>
  {/strip}{/capture}
{/if}
{*<a target="_blank" href="index.php?target=import&mode=flexible_import_profile&profile_id={$profile.id}">*}
{$profile_name}
{*</a>*}
</td>
<td>{if $profile.active_reccuring eq 1}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}</td>
<td>{if $profile.recurring_import_path ne ''}<a href='index.php?target=datahub_profile_files&profile_id={$profile.id}' target='_blank'>{$profile.recurring_import_path} (see files)</a>{else}&nbsp;{/if}</td>
{*<td>{if $profile.recurring_import_days gt 0}{$profile.recurring_import_days}&nbsp;{$lng.lbl_days}{/if}&nbsp;{if $profile.recurring_import_hours gt 0}{$profile.recurring_import_hours}&nbsp;{$lng.lbl_hours|default:'hours'}{/if}</td>*}
<td>{if $profile.recurring_last_run_date gt 0}{$profile.recurring_last_run_date|date_format:$config.Appearance.datetime_format}{else}{$lng.lbl_never|default:'Never'}{/if}</td>
{*<td><a target="_blank" href="index.php?target=import&mode=flexible_import_profile&profile_id={$profile.id}">{$lng.lbl_edit}</a></td>*}
</tr>{/foreach}
</table>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block1 title=$dh_main_display_title}

{capture name=block2}
<table id="log_entries_tbl" width='100%' cellpadding='3' cellspacing='0' height="200px" class="table table-striped">
<thead>
<tr>
<th width="10%">{$lng.lbl_date}</th>
<th width="10%">{$lng.lbl_event_type|default:'Type'}</th>
<th width="15%">{$lng.lbl_source|default:'Source'}</th>
<th width="65%">{$lng.lbl_message}</th>
</tr>
</thead>
{foreach from=$datahub_log_entries item=le}
{capture assign='row_type_color'}
style="color: {if $le.event_type eq 'E'}red{elseif $le.event_type eq 'I'}blue{else}green{/if}"
{/capture}
<tr>
<td>{$le.date|date_format:$config.Appearance.datetime_format}</td>
<td><span {$row_type_color}>{$le.event_type_name}</span></td>
<td><span {$row_type_color}>{$le.event_source}</span></td>
<td><span {$row_type_color}>{$le.event_message}</span></td>
</tr>
{/foreach}
</table>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2 title=$lng.lbl_logged_datahub_actions|default:'Logged Datahub Actions'}


{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_initial_feeds_import|default:'Initial Feeds Import'}
