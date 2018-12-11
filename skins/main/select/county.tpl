{tunnel func='cw_map_get_counties_smarty' country=$for_country assign='counties'}
<select name="{$name}">
{if $required eq 'N'}
	<option value="">{$lng.lbl_please_select}</option>
{/if}
{if $required eq 'O'}
	<option value="{if $value_for_other ne "no"}Other{/if}"{if $default eq "Other"} selected="selected"{/if}>{$lng.lbl_other}</option>
{/if}
{foreach from=$counties item=county}
	<option value="{$county.county_id}"{if $default eq $county.county_id} selected="selected"{/if}>{$county.country_code}: {$county.county|amp}</option>
{/foreach}
</select>
