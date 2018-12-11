{if $profile_fields.basic.ssn.is_avail}
<div class="form-group input_field_{$profile_fields.basic.ssn.is_required}">
    <label class="col-xs-12">{$lng.lbl_ssn}</label>
    <div class="col-xs-6 col-md-3">
    	<input type="text" class="form-control" name="update_fields[basic][ssn]" maxlength="32" value="{$userinfo.additional_info.ssn}" />
    	{if $fill_error.basic.ssn}<font class="field_error">&lt;&lt;{*$lng.err_field_ssn*}</font>{/if}
    </div>
</div>
{/if}
