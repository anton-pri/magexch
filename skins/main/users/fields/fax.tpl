{if $profile_fields.address.fax.is_avail}
<div class="input_field_{$profile_fields.address.fax.is_required}">
    <label {if $profile_fields.address.fax.is_required}class='required'{/if}>{$lng.lbl_fax}</label>
    <input type="text" id="{$name_prefix|id}_fax" name="{$name_prefix}[fax]"  class='short{if $profile_fields.address.fax.is_required} required{/if}' maxlength="128" value="{$address.fax}"{if $readonly} disabled{/if} />
    {if $fill_error.address.fax}<span class="field_error">&lt;&lt;{*$lng.err_field_fax*}</span>{/if}
</div>
{/if}
