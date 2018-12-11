{if $profile_fields.address.fax.is_avail}
<div class="form-group input_field_{$profile_fields.address.fax.is_required}">
    <label class='col-xs-12 {if $profile_fields.address.fax.is_required}required{/if}'>{$lng.lbl_fax}</label>
    <div class="col-xs-6 col-md-3">
    	<input type="text" id="{$name_prefix|id}_fax" name="{$name_prefix}[fax]"  class='form-control {if $profile_fields.address.fax.is_required} required{/if}' maxlength="128" value="{$address.fax}"{if $readonly} disabled{/if} />
    	{if $fill_error.address.fax}<span class="field_error">&lt;&lt;{*$lng.err_field_fax*}</span>{/if}
    </div>
</div>
{/if}
