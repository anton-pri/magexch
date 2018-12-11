{tunnel func='cw_map_get_regions_smarty' country=$country assign='regions'}
<select name="{$name}">
{if $required eq 'N'}
	<option value="">{$lng.lbl_please_select}</option>
{/if}
{if $required eq 'O'}
	<option value="{if $value_for_other ne "no"}Other{/if}"{if $default eq "Other"} selected="selected"{/if}>{$lng.lbl_other}</option>
{/if}
{foreach from=$regions item=region}
	<option value="{$region.region_id}"{if $value eq $region.region_id} selected="selected"{/if}>{$region.region}</option>
{/foreach}
</select>
