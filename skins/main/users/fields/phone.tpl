{if $profile_fields.address.phone.is_avail}
<div class="input_field_{$profile_fields.address.phone.is_required}">
    <label {if $profile_fields.address.phone.is_required}class='required'{/if}>{$lng.lbl_phone}</label>
    <input type="text" id="{$name_prefix|id}_phone" name="{$name_prefix}[phone]"  class='short{if $profile_fields.address.phone.is_required} required{/if}' maxlength="32" value="{$address.phone}"{if $readonly} disabled{/if} />
    {if $fill_error.address.phone}<span class="field_error">&lt;&lt;{*$lng.err_field_phone*}</span>{/if}
</div>
{/if}
