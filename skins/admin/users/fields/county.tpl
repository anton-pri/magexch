{if $profile_fields.address.county.is_avail}
<div class="form-group input_field_{$profile_fields.address.county.is_required}">
    <label class="col-xs-12">{$lng.lbl_county}</label>
    <div class="col-xs-12">
    	{include file='main/map/counties.tpl' name="`$name_prefix`[county]" default=$address.county city_name="`$name_prefix`[county]" county_value=$address.county}
    	{if $fill_error.address.county}<span class="field_error">&lt;&lt;{*$lng.err_field_county*}</span>{/if}
    </div>
</div>
{/if}
