{if $profile_fields.address.firstname.is_avail}
<div class="form-group input_field_{$profile_fields.address.firstname.is_required}">
    <label class='col-xs-12 {if $profile_fields.address.firstname.is_required}required{/if}'>{$lng.lbl_firstname}</label>
    <div class="col-xs-12">
    	<input type="text" name="{$name_prefix}[firstname]" class='form-control {if $profile_fields.address.firstname.is_required}required {/if}short' id="{$name_prefix|id}_firstname" maxlength="32" value="{$address.firstname}"{if $readonly} disabled{/if} />
    	{if $fill_error.address.firstname}<span class="field_error">&lt;&lt;{*$lng.err_field_firstname*}</span>{/if}
    </div>
</div>
{/if}
