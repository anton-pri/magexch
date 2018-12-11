{if $ps_zones}
{if $ps_conds.A.zones ne ''}
<script type="text/javascript">
//<!--
{foreach from=$ps_conds.A.zones item='zone_data'}
ps_zones[{$zone_data.id}] = 1;
{/foreach}
-->
</script>
{/if}
<select id="ps-zones-selector" class="form-control push-20" name="ps_conds[A][zones][]" size="10" multiple="multiple">
{section name=zone loop=$ps_zones}
{assign var='zone_id' value=$ps_zones[zone].zone_id}
<option value="{$zone_id}" title="{strip}{if $ps_zones[zone].elements}
{section name=el loop=$ps_zones[zone].elements}
{strip}
{if $ps_zones[zone].elements[el].element_name ne ""}
{$ps_zones[zone].elements[el].element_name}
{else}
{if $ps_zones[zone].elements[el].element_type eq "C"}{$lng.lbl_countries}:
{elseif $ps_zones[zone].elements[el].element_type eq "S"}{$lng.lbl_states}:
{elseif $ps_zones[zone].elements[el].element_type eq "G"}{$lng.lbl_counties}:
{elseif $ps_zones[zone].elements[el].element_type eq "T"}{$lng.lbl_city_masks}:
{elseif $ps_zones[zone].elements[el].element_type eq "Z"}{$lng.lbl_zipcode_masks}:
{elseif $ps_zones[zone].elements[el].element_type eq "A"}{$lng.lbl_address_masks}:
{/if}
{$ps_zones[zone].elements[el].counter}
{/if}
{if not %el.last%},{/if}
{/strip}
{/section}
{else}
{$lng.txt_zone_is_empty}
{/if}{/strip}"{if $ps_conds.A.zones}{if $ps_conds.A.zones[$zone_id] ne ''} selected = "selected"{/if}{/if}>{$ps_zones[zone].zone_name}</option>
{/section}
</select>
{/if}