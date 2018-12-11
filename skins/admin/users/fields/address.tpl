{if $profile_fields.address.address.is_avail}
<div class="form-group input_field_{$profile_fields.address.address.is_required}">
    <label class='col-xs-12 {if $profile_fields.address.address.is_required}required{/if}'>{$lng.lbl_address}</label>
    <div class="col-xs-12">
    	<input type="text" class="form-control" id="{$name_prefix|id}_address" name="{$name_prefix}[address]"  maxlength="64" value="{$address.address}"{if $readonly} disabled{/if}  class='long {if $profile_fields.address.address.is_required}required{/if}' />
    	{if $fill_error.address.address}<span class="field_error">&lt;&lt;{*$lng.err_field_address*}</span>{/if}
    </div>
</div>
{/if}
