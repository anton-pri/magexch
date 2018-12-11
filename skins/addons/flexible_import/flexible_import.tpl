{capture name=section}

{capture name=block1}

<p>{$lng.txt_flexible_import_text}</p>

<div class="Error" style="color:#4e0202;">{$err_msg}</div>

<div class="form-horizontal">
    <form enctype="multipart/form-data" action="index.php?target={$current_target}&mode=flexible_import" method="post" name="process_import">
    <input type="hidden" name="action" value="import_file" />
    <div class="form-group">
        <label class='required col-xs-12'>{$lng.lbl_select_profile}</label>
        <div class="col-xs-12">
            <select class="required" name="profile_id">
                <option value="">{$lng.lbl_please_select}</option>
                {foreach from=$profiles item=p}
                   {if $p.import_src_type ne 'T'}
                    <option value="{$p.id}">{$p.name}</option>
                   {/if}
                {/foreach}
            </select>
        </div> 
    </div>
    <div class="form-group">
        <div class="col-xs-12">
            <label class='required col-xs-12'>{$lng.lbl_import_data}</label>
            <div class="col-xs-12"><input type='radio' name='import_type' value='pc' />&nbsp;{$lng.lbl_from_your_pc}</div>
            <div class="col-xs-12"><input type='radio' name='import_type' value='server' />&nbsp;{$lng.lbl_from_server}</div>
        </div>
    </div>

    <div class="form-group import_from" id="import_from_pc" style="display:none;">
        <div class="col-xs-12">
            <input name="import_file" type="file" />
        </div>
    </div>
    <div class="form-group import_from" id="import_from_server" style="display:none;">
       <table class="table table-striped" width="100%">
       {if $search_prefilled.files}
        {assign var="i" value=0}
        {section name=ind loop=$search_prefilled.files}
          {if $i<7}
            <tr>
              <td>
                <input type="checkbox" name="server_filenames[{$i++}]" value="{$search_prefilled.files[ind]}" />
              </td>
              <td>
                {$search_prefilled.files[ind]}
              </td>
            </tr>
          {/if}
        {/section}
        {else}
             <tr><td>{$lng.lbl_no_files_to_import}</td></tr>
        {/if}
        </table>
    </div>
    <div class="button_left_align buttons">
        {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('process_import');" button_title=$lng.lbl_import acl='__1300' style='btn-green push-20 push-5-r'}
        &nbsp;&nbsp;
        {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('process_import', 'delete_files');" button_title=$lng.lbl_delete acl='__1300' style='btn-danger push-20 push-5-r'}
        <div class="clear"></div>
    </div>

</form>
</div>
{/capture}
{if $smarty.get.all_buttons eq 'Y' || 1}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block1 extra='width="100%"' title=$lng.lbl_flexible_import_manual}
{/if}

{capture name=block2}
<div class="box">
    {*include file="common/subheader.tpl" title=$lng.lbl_profiles*}
    <form action="index.php?target={$current_target}&mode=flexible_import_profile" method="post" name="process_profiles">
    <input type="hidden" name="action" value="delete_profile" />
        {include file='common/navigation.tpl'}

        <table  class="table table-striped" width="100%">

        <thead><tr>
            <th width="5%" align="center"><input type='checkbox' class='select_all' class_to_select='profile_id' /></th>
{*
            <th width="8%">{if $search_prefilled.sort_field eq "id"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&amp;mode=flexible_import&amp;sort=id&amp;sort_direction={$search_prefilled.sort_direction}">#{$lng.lbl_id}</a></th>
*}
            <th width="30%" style="text-align:center;">{if $search_prefilled.sort_field eq "name"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&amp;mode=flexible_import&amp;sort=name&amp;sort_direction={$search_prefilled.sort_direction}">{$lng.lbl_name}</a></th>
{*
            <th width="10%">{if $search_prefilled.sort_field eq "type"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&amp;mode=flexible_import&amp;sort=type&amp;sort_direction={$search_prefilled.sort_direction}">{$lng.lbl_type}</a></th>
*}
            <th width="60%" style="text-align:center;">{$lng.lbl_description}</th>
            <th width="5%">{$lng.lbl_recurring_run|default:'Recurring Options'}</th>
        </tr></thead>

            {foreach from=$profiles item=p}
                <tr{cycle values=", class='cycle'"}>
                    <td align="center"><input type="checkbox" name="profile_ids[]"  value ="{$p.id}" class="profile_id" /></td>
{*
                    <td><a href="index.php?target=import&mode=flexible_import_profile&profile_id={$p.id}">{$p.id}</a></td>
*}
                    <td align="center"><a href="index.php?target=import&mode=flexible_import_profile&profile_id={$p.id}"><b>{$p.name}</b></a>
                      {if $p.import_src_type eq 'T'}&nbsp;(not to be run manually){/if}
                    </td>
{*
                    <td>{$p.type}</td>
*}
                    <td>{$p.description}</td>
                    <td align="center">{if !$p.active_reccuring || (!$p.recurring_import_days && !$p.recurring_import_hours)}manual{else}
                      Runs every 
                      {if $p.recurring_import_days}{$p.recurring_import_days} day{if $p.recurring_import_days gt 1}s{/if}{/if} 
                      {if $p.recurring_import_hours}{$p.recurring_import_hours} hour{if $p.recurring_import_hours gt 1}s{/if}{/if} 
                    {/if}</td>
                </tr>
                {/foreach}
     </table>

        <div class="buttons">
            {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('process_profiles', 'delete_profile');" button_title=$lng.lbl_delete_selected style='btn-danger push-20 push-5-r'}
            {include file='admin/buttons/button.tpl' href="index.php?target=`$current_target`&mode=flexible_import_profile" button_title=$lng.lbl_add_new acl='__1200' style='btn-green push-20 push-5-r'}
        </div>
        <div class="clear"></div>
    </form>
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2 extra='width="100%"' title=$lng.lbl_profiles}

{capture name=block3}
<div class="box">
{tunnel func='cw_flexible_import_log_list_files' via='cw_call' assign='fi_log_files'}
{if $fi_log_files}
{*$fi_log_files|@debug_print_var*}
<table  class="table table-striped" width="100%">
    <thead><tr>
        <th width="80%" align="center">{$lng.lbl_log_name|default:'Log Name'}</th>
        <th>&nbsp;</th> 
    </tr></thead>
{foreach from=$fi_log_files item=lf}
    {foreach from=$lf item=lfname}
    <tr>
        <td><a target="_blank" href="index.php?target=get_log&file={$lfname}">{$lfname}</a></td>
        <td><a href="index.php?target=get_log&file={$lfname}&action=delete">{$lng.lbl_delete}</a></td>
    </tr>
    {/foreach}
{/foreach}
</table>
{else}
{$lng.lbl_no_flexible_import_logs_found|default:'There are no import logs found.'}
<br><br>
{/if}
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block3 extra='width="100%"' title=$lng.lbl_import_logs|default:'Import Logs'}

<!-- cw@extra_flexible_import_controls [ -->

<!-- cw@extra_flexible_import_controls ] -->

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_flexible_import_profiles_management|default:'Flexible Import Profiles Management'}
