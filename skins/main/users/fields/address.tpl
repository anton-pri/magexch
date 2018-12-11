{if $profile_fields.address.address.is_avail}
<div class="input_field_{$profile_fields.address.address.is_required}">
    <label {if $profile_fields.address.address.is_required}class='required'{/if}>{$lng.lbl_address}</label>
    <input type="text" id="{$name_prefix|id}_address" name="{$name_prefix}[address]"  maxlength="64" value="{$address.address}"{if $readonly} disabled{/if}  class='long {if $profile_fields.address.address.is_required}required{/if}' />
    {if $fill_error.address.address}<span class="field_error">&lt;&lt;{*$lng.err_field_address*}</span>{/if}
</div>
{/if}
