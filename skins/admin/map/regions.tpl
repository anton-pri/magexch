{*include file="common/subheader.tpl" title=$country_info.country*}
{capture name=section}
{capture name=block}

{include file="common/navigation.tpl"}

<form action="index.php?target={$current_target}" method="post" name="regions_form">
<input type="hidden" name="mode" value="regions" />
<input type="hidden" name="action" value="update" />
<input type="hidden" name="country" value="{$country}" />
<input type="hidden" name="page" value="{$smarty.get.page}" />

<div class="box">
<table class="table table-striped dataTable vertical-center">
<thead>
<tr>
    <th width="5%" class="text-center"><input type='checkbox' class='select_all' class_to_select='regions_item' /></th>
	<th>{$lng.lbl_region}</th>
</tr>
</thead>
{if $regions}
{foreach from=$regions item=region}
<tr{cycle values=", class='cycle'"}>
    <td align="center"><input type="checkbox" name="selected[{$region.region_id}]" class="regions_item" /></td>
    <td><input  class="form-control" type="text" size="50" name="posted_data[{$region.region_id}][region]" value="{$region.region|escape}" /></td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="2" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
<thead>
<tr>
    <th colspan="2">{$lng.lbl_add_new}</th>
</tr>
</thead>
<tr>
    <td>&nbsp;</td>
    <td><input class="form-control" type="text" size="50" name="posted_data[0][region]" value="" /></td>
</tr>
</table>
</div>
<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_update href="javascript:cw_submit_form(document.regions_form, 'update')" style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form(document.regions_form, 'delete')" style="btn-danger push-20 push-5-r"}
</div>
</form>

{include file="common/navigation.tpl"}
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$country_info.country}

