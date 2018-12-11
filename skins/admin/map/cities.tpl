{*include file="common/subheader.tpl" title=$country_info.country*}
{capture name=section}
{capture name=block}

{include file="common/navigation.tpl"}

<form action="index.php?target={$current_target}" method="post" name="cities_form">
<input type="hidden" name="mode" value="cities" />
<input type="hidden" name="action" value="update" />
<input type="hidden" name="country" value="{$country}" />
<input type="hidden" name="page" value="{$smarty.get.page}" />

<div class="box">
<table class="table table-striped dataTable vertical-center">
<thead>
<tr>
    <th width="5%" class="text-center"><input type='checkbox' class='select_all' class_to_select='cities_item' /></th>
    <th>
{if $country_info.display_counties}
    {$lng.lbl_county}
{else}
    {$lng.lbl_state}
{/if}
	<th>{$lng.lbl_city}</th>
</tr>
</thead>
{if $cities}
{foreach from=$cities item=city}
<tr{cycle values=", class='cycle'"}>
    <td align="center"><input type="checkbox" name="selected[{$city.city_id}]" class="cities_item" /></td>
{if $country_info.display_counties}
    <td>{include file='admin/select/county.tpl' name="posted_data[`$city.city_id`][county_id]" default=$city.county_id default_country=$country for_country=$country}</td>
{else}
    <td>{include file='admin/select/state.tpl' name="posted_data[`$city.city_id`][state_id]" default=$city.state_id default_country=$country required='Y' for_country=$country identity='state_id'}</td>
{/if}
    <td><input type="text" class="form-control" size="50" name="posted_data[{$city.city_id}][city]" value="{$city.city|escape}" /></td>
</tr>
{/foreach}

{else}
<tr>
    <td colspan="3" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
<thead>
<tr>
    <th colspan="3">{$lng.lbl_add_new}</th>
</tr>
</thead>
<tr>
    <td>&nbsp;</td>
{if $country_info.display_counties}
    <td>{include file='admin/select/county.tpl' name="posted_data[0][county_id]" default='' default_country=$country for_country=$country}</td>
{else}
    <td>{include file='admin/select/state.tpl' name="posted_data[0][state_id]" default='' required='Y' default_country=$country for_country=$country identity='state_id'}</td>
{/if}
    <td><input type="text" size="50" class="form-control" name="posted_data[0][city]" value="" /></td>
</tr>
</table>
</div>
<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_update href="javascript:cw_submit_form(document.cities_form, 'update')" style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form(document.cities_form, 'delete')" style="btn-danger push-20 push-5-r"}
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}


{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$country_info.country}

