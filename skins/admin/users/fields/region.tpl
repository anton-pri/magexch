{if $profile_fields.address.region.is_avail}
<div class="form-group input_field_{$profile_fields.address.region.is_required}">
    <label class="col-xs-12">{$lng.lbl_region}</label>
    <div class="col-xs-12">
    	{include file='main/map/regions.tpl' name="`$name_prefix`[region]" default=$address.region state_name="`$name_prefix`[state]" state_value=$address.state county_name="`$name_prefix`[county]" county_value=$address.county country_value=$address.country}
    	{if $fill_error.address.region}<span class="field_error">&lt;&lt;{*$lng.err_field_region*}</span>{/if}
    </div>
</div>
{/if}
