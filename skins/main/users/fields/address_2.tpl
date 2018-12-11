{if $profile_fields.address.address_2.is_avail}
<div class="input_field_{$profile_fields.address.address_2.is_required}">
    <label {if $profile_fields.address.address_2.is_required}class='required'{/if}>{$lng.lbl_address_2}</label>
    <input type="text" id="{$name_prefix|id}_address_2" name="{$name_prefix}[address_2]" class='long {if $profile_fields.address.address_2.is_required}required{/if}' maxlength="64" value="{$address.address_2}"{if $readonly} disabled{/if} />
    {if $fill_error.address.address_2}<span class="field_error">&lt;&lt;{*$lng.err_field_address_2*}</span>{/if}
</div>
{/if}
