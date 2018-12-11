{if $profile_fields.address.zipcode.is_avail}
<div class="form-group input_field_{$profile_fields.address.zipcode.is_required}">
    <label class='col-xs-12 {if $profile_fields.address.zipcode.is_required}required{/if}'>{$lng.lbl_zipcode}</label>
    <div class="col-xs-6 col-md-3">
    	<input type="text" id="{$name_prefix|id}_zipcode" name="{$name_prefix}[zipcode]"  class='form-control {if $profile_fields.address.zipcode.is_required}required {/if}short' maxlength="32" value="{$address.zipcode}" {if $readonly} disabled{/if} />
    	{if $fill_error.address.zipcode}<span class="field_error">&lt;&lt;{*$lng.err_field_zipcode*}</span>{/if}
    </div>
</div>
{/if}
