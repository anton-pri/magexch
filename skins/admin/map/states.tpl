{*include file="common/subheader.tpl" title=$country_info.country*}
{capture name=section}
{capture name=block}

<p>{$lng.txt_states_management_top_text}</p>

{include file="common/navigation.tpl"}

<form action="index.php?target={$current_target}" method="post" name="states_form">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="mode" value="states" />
<input type="hidden" name="country" value="{$country}" />
<input type="hidden" name="page" value="{$smarty.get.page}" />

<div class="box">
<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
    <th class="text-center" width="1%"><input type='checkbox' class='select_all' class_to_select='states_item' /></th>
{if $country_info.display_regions}
	<th width="10%" class="text-center" >{$lng.lbl_region}</th>
{/if}
	<th width="10%" class="text-center" >{$lng.lbl_code}</th>
	<th width="60%">{$lng.lbl_state}</th>
</tr>
</thead>
{if $states ne ""}
{foreach from=$states item=state}
<tr {cycle values=", class='cycle'"}>
    <td align="center"><input type="checkbox" name="selected[{$state.state_id}]" class="form-control" /></td>
{if $country_info.display_regions}
    <td align="center">{include file='main/select/region.tpl' name="posted_data[`$state.state_id`][region_id]" value=$state.region_id country=$country}</td>
{/if}
	<td align="center"><input class="form-control" type="text" size="6" name="posted_data[{$state.state_id}][code]" value="{$state.code|escape}" /></td>
	<td><input type="text" class="form-control" size="30" name="posted_data[{$state.state_id}][state]" value="{$state.state}" style="width: 99%;" /></td>
</tr>
{/foreach}

{else}
<tr>
    <td colspan="4" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
<thead>
<tr>
    <th colspan="4">{$lng.lbl_add_new}</th>
</tr>
</thead>
<tr>
    <td>&nbsp;</td>
{if $country_info.display_regions}
    <td align="center">{include file='main/select/region.tpl' name="posted_data[0][region_id]" value=0 country=$country}</td>
{/if}
    <td align="center"><input class="form-control" type="text" size="6" name="posted_data[0][code]" value="" /></td>
    <td><input class="form-control" type="text" size="30" name="posted_data[0][state]" value="" style="width: 99%;" /></td>
</tr>
</table>
</div>
<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_update href="javascript:cw_submit_form(document.states_form, 'update')" style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form(document.states_form, 'delete')" style="btn-danger push-20 push-5-r"}
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$country_info.country}

