{if $profile_fields.address.city.is_avail}
<div class="form-group input_field_{$profile_fields.address.city.is_required}">
    <label class='col-xs-12 {if $profile_fields.address.city.is_required}required{/if}'>{$lng.lbl_city}</label>
    <div class="col-xs-12">
    	{include file='main/map/cities.tpl' name="`$name_prefix`[city]' value=$address.city}
    	{if $fill_error.address.city}<span class="field_error">&lt;&lt;{*$lng.err_field_city*}</span>{/if}
    </div>
</div>
{/if}
