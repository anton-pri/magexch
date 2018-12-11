{if $profile_fields.basic.ssn.is_avail}
<div class="input_field_{$profile_fields.basic.ssn.is_required}">
    <label>{$lng.lbl_ssn}</label>
    <input type="text" name="update_fields[basic][ssn]" maxlength="32" value="{$userinfo.additional_info.ssn}" />
    {if $fill_error.basic.ssn}<font class="field_error">&lt;&lt;{*$lng.err_field_ssn*}</font>{/if}
</div>
{/if}
