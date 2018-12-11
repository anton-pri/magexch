<select name="{$name}" {if $disabled} disabled{/if} class="form-control">
    <option value='1'{if $value eq '1'} selected="selected"{/if}>{$lng.lbl_enabled}</option>
    <option value='0'{if $value eq '0'} selected="selected"{/if}>{$lng.lbl_disabled}</option>
</select>
