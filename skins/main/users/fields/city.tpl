{if $profile_fields.address.city.is_avail}
<div class="input_field_{$profile_fields.address.city.is_required}">
    <label {if $profile_fields.address.city.is_required}class='required'{/if}>{$lng.lbl_city}</label>
    {include file='main/map/cities.tpl' name="`$name_prefix`[city]' value=$address.city}
    {if $fill_error.address.city}<span class="field_error">&lt;&lt;{*$lng.err_field_city*}</span>{/if}
</div>
{/if}
