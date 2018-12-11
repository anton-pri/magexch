{if $profile_fields.address.firstname.is_avail}
<div class="input_field_{$profile_fields.address.firstname.is_required}">
    <label {if $profile_fields.address.firstname.is_required}class='required'{/if}>{$lng.lbl_firstname}</label>
    <input type="text" name="{$name_prefix}[firstname]" class='{if $profile_fields.address.firstname.is_required}required {/if}short' id="{$name_prefix|id}_firstname" maxlength="32" value="{$address.firstname}"{if $readonly} disabled{/if} />
    {if $fill_error.address.firstname}<span class="field_error">&lt;&lt;{*$lng.err_field_firstname*}</span>{/if}
</div>
{/if}
