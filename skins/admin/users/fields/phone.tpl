{if $profile_fields.address.phone.is_avail}
<div class="form-group input_field_{$profile_fields.address.phone.is_required}">
    <label class='col-xs-12 {if $profile_fields.address.phone.is_required}required{/if}'>{$lng.lbl_phone}</label>
    <div class="col-xs-6 col-md-3">
    	<input type="text" id="{$name_prefix|id}_phone" name="{$name_prefix}[phone]"  class='form-control {if $profile_fields.address.phone.is_required} required{/if}' maxlength="32" value="{$address.phone}"{if $readonly} disabled{/if} />
    	{if $fill_error.address.phone}<span class="field_error">&lt;&lt;{*$lng.err_field_phone*}</span>{/if}
    </div>
</div>
{/if}
