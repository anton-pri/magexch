{*include file="common/subheader.tpl" title=$country_info.country*}
{capture name=section}
{capture name=block}

{include file="common/navigation.tpl"}

<form action="index.php?target={$current_target}" method="post" name="counties_form">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="mode" value="counties" />
<input type="hidden" name="country" value="{$country}" />
<input type="hidden" name="page" value="{$smarty.get.page}" />

<div class="box">
<table class="table table-striped dataTable vertical-center">
<thead>
<tr>
    <th width="5%" class="text-center"><input type='checkbox' class='select_all' class_to_select='counties_item' /></th>
    <th>{$lng.lbl_state}</th>
	<th>{$lng.lbl_county}</th>
</tr>
</thead>
{if $counties}
{foreach from=$counties item=county}
<tr{cycle values=", class='cycle'"}>
    <td align="center"><input type="checkbox" name="selected[{$county.county_id}]" class="counties_item" /></td>
    <td>{include file='admin/select/state.tpl' name="posted_data[`$county.county_id`][state_id]" default=$county.state_id default_country=$country required='Y' identity='state_id'}</td>
    <td><input type="text" class="form-control" size="50" name="posted_data[{$county.county_id}][county]" value="{$county.county|escape}" /></td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="3" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
<thead>
<tr>
    <th colspan="3"><br />{$lng.lbl_add_new}</th>
</tr>
</thead>
<tr>
    <td>&nbsp;</td>
    <td>{include file='admin/select/state.tpl' name='posted_data[0][state_id]' required='Y' identity='state_id'}</td>
    <td><input class="form-control" type="text" size="50" name="posted_data[0][county]" value="" /></td>
</tr>
</table>
</div>

{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_update href="javascript:cw_submit_form(document.counties_form, 'update')" style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form(document.counties_form, 'delete')" style="btn-green push-20 push-5-r"}

</form>

{include file="common/navigation.tpl"}
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$country_info.country}

