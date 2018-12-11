{if $profile_fields.address.state.is_avail}
<div class="form-group input_field_{$profile_fields.address.state.is_required}">
    <label class='col-xs-12 {if $profile_fields.address.state.is_required}required{/if}'>{$lng.lbl_state}</label>
    <div class="col-xs-12">
    	{include file='main/map/_states.tpl' name="`$name_prefix`[state]" default=$address.state}
    	{if $fill_error.address.state}<span class="field_error">&lt;&lt;{*$lng.err_field_state*}</span>{/if}
    </div>
</div>
{/if}
