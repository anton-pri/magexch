{capture name=section}
{capture name=block}

<form action="index.php?target={$current_target}" method="post" name="zones_form">
<input type="hidden" name="action" value="delete" />

<div class="box">

<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
	<th width="10"><input type='checkbox' class='select_all' class_to_select='zones_item' /></th>
	<th width="20%">{$lng.lbl_zone_name}</th>
	<th width="80%">{$lng.txt_note}</th>
</tr>
</thead>
<tr{cycle values=", class='cycle'"}>
	<td><input type="checkbox" disabled="disabled" /></td>
	<td>{$lng.lbl_zone_default}</td>
	<td>{$lng.lbl_all_addresses}</td>
</tr>

{if $zones}
{section name=zone loop=$zones}
<tr {cycle values=", class='cycle'"}>
	<td><input type="checkbox" name="to_delete[{$zones[zone].zone_id}]" class="zones_item" /></td>
	<td><span {edit_on_place table='zones' field='zone_name' pk=$zones[zone].zone_id}><a href="index.php?target={$current_target}&zone_id={$zones[zone].zone_id}">{$zones[zone].zone_name}</a></span></td>
	<td>
{if  $zones[zone].elements}
{section name=el loop=$zones[zone].elements}
{strip}
{if $zones[zone].elements[el].element_name ne ""}
{$zones[zone].elements[el].element_name}
{else}
{if $zones[zone].elements[el].element_type eq "C"}{$lng.lbl_countries}:
{elseif $zones[zone].elements[el].element_type eq "S"}{$lng.lbl_states}:
{elseif $zones[zone].elements[el].element_type eq "G"}{$lng.lbl_counties}:
{elseif $zones[zone].elements[el].element_type eq "T"}{$lng.lbl_city_masks}:
{elseif $zones[zone].elements[el].element_type eq "Z"}{$lng.lbl_zipcode_masks}:
{elseif $zones[zone].elements[el].element_type eq "A"}{$lng.lbl_address_masks}:
{/if}
{$zones[zone].elements[el].counter}
{/if}
{if not %el.last%},{/if}
{/strip}
{/section}
{else}
{$lng.txt_zone_is_empty}
{/if}
	</td>
</tr>

{/section}
{/if}
</table>

</div> 
<div class="buttons">
{if $zones}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript: cw_submit_form('zones_form');" style="btn-green push-20 push-5-r"}
{/if}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=`$current_target`&mode=add" style="btn-green push-20"}
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$zone_type_zones_name|default:$lng.lbl_destination_zones}
