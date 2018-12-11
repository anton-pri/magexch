{if $profile_fields.address.lastname.is_avail}
<div class="input_field_{$profile_fields.address.lastname.is_required}">
    <label {if $profile_fields.address.lastname.is_required}class='required'{/if}>{$lng.lbl_lastname}</label>
    <input type="text" name="{$name_prefix}[lastname]" class='short{if $profile_fields.address.lastname.is_required} required{/if}' id="{$name_prefix|id}_lastname" maxlength="32" value="{$address.lastname}"{if $readonly} disabled{/if} />
    {if $fill_error.address.lastname}<span class="field_error">&lt;&lt;{*$lng.err_field_lastname*}</span>{/if}
</div>
{/if}
