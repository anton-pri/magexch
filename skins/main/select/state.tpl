{tunnel func='cw_map_get_states_smarty' country=$for_country assign='states' load='map'}
<select name="{$name}"{if $is_disabled} disabled{/if}>
{if $required eq 'N'}
	<option value="">{$lng.lbl_please_select}</option>
{/if}
{if $required eq 'O'}
	<option value="{if $value_for_other ne "no"}Other{/if}"{if $default eq "Other"} selected="selected"{/if}>{$lng.lbl_other}</option>
{/if}
{section name=state_idx loop=$states}
    {if $identity eq 'state_id'}
        <option value="{$states[state_idx].state_id}"{if $default eq $states[state_idx].state_id} selected="selected"{/if}>{if !$for_country}{$states[state_idx].country_code}: {/if}{$states[state_idx].state|amp}</option>
    {else}
        <option value="{$states[state_idx].state_code}"{if $default eq $states[state_idx].state_code && (!$default_country || $default_country eq $states[state_idx].country_code)} selected="selected"{/if}>{if !$for_country}{$states[state_idx].country_code}: {/if}{$states[state_idx].state|amp}</option>
    {/if}
{/section}
</select>
