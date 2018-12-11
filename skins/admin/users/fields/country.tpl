{if $profile_fields.address.country.is_avail}
<div class="form-group input_field_{$profile_fields.address.country.is_required}">
    <label class="col-xs-12">{$lng.lbl_country}</label>
    <div class="col-xs-12">
    	{include file='main/map/_countries.tpl' name="`$name_prefix`[country]" default=$address.country}
    	{if $fill_error.address.country}<span class="field_error">&lt;&lt;{*$lng.err_field_country*}</span>{/if}
    </div>
</div>
{/if}
