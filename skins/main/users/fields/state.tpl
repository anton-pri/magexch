{if $profile_fields.address.state.is_avail}
<div class="input_field_{$profile_fields.address.state.is_required}">
    <label {if $profile_fields.address.state.is_required}class='required'{/if}>{$lng.lbl_state}</label>
    {include file='main/map/_states.tpl' name="`$name_prefix`[state]" default=$address.state}
    {if $fill_error.address.state}<span class="field_error">&lt;&lt;{*$lng.err_field_state*}</span>{/if}
</div>
{/if}
